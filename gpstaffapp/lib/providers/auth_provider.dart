import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../api/api_client.dart';
import '../models/user_model.dart';

class AuthProvider with ChangeNotifier {
  UserModel? _user;
  String? _token;
  bool _loading = true;

  UserModel? get user => _user;
  String? get token => _token;
  bool get loading => _loading;
  bool get isAuthenticated => _token != null && _token!.isNotEmpty;

  AuthProvider() {
    loadStorageData();
  }

  Future<void> loadStorageData() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final savedToken = prefs.getString('@token');
      final savedUserJson = prefs.getString('@user');

      if (savedToken != null && savedUserJson != null) {
        _token = savedToken;
        final Map<String, dynamic> userMap = jsonDecode(savedUserJson);
        _user = UserModel.fromJson(userMap);
      }
    } catch (e) {
      debugPrint('Auth storage load error: $e');
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  Future<Map<String, dynamic>> login(String username, String password) async {
    try {
      final dio = ApiClient().dio;
      final response = await dio.post(
        '/admin/login',
        data: {
          'username': username,
          'password': password,
        },
      );

      final data = response.data;
      if (data is Map<String, dynamic> && (data['status'] == true || data['status'] == 1 || data['token'] != null)) {
        final tokenStr = (data['token'] ?? '').toString();
        final userObj = data['user'] is Map<String, dynamic>
            ? data['user'] as Map<String, dynamic>
            : <String, dynamic>{'username': username};

        _token = tokenStr;
        _user = UserModel.fromJson(userObj);

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('@token', tokenStr);
        await prefs.setString('@user', jsonEncode(userObj));

        notifyListeners();
        return {'success': true};
      }

      final message = (data is Map ? data['message'] : null) ?? 'Invalid username or password.';
      return {'success': false, 'message': message.toString()};
    } on DioException catch (e) {
      String msg = 'Connection error. Please check your internet connection.';
      if (e.response?.data != null && e.response?.data is Map) {
        msg = e.response?.data['message']?.toString() ?? msg;
      }
      return {'success': false, 'message': msg};
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  Future<void> logout() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('@token');
      await prefs.remove('@user');
    } catch (e) {
      debugPrint('Logout error: $e');
    }
    _token = null;
    _user = null;
    notifyListeners();
  }
}
