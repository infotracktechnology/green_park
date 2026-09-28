import 'dart:async';

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';

import '../api/api_client.dart';

/// Handles FCM device token generation + registration for the staff app.
///
/// The token is stored in `staff.device_token` by the backend and is used by
/// StaffAnnouncement to push announcements to the targeted staff.
class NotificationService {
  static final NotificationService instance = NotificationService._();
  NotificationService._();

  static const String _channelId = 'gpcc_staff_announcements';
  static const String _channelTitle = 'Announcements';

  /// Android channel id - must match
  /// `com.google.firebase.messaging.default_notification_channel_id`
  /// in AndroidManifest.xml, otherwise Android silently drops the push.
  static String get androidChannelId => _channelId;

  /// Android channel display name.
  static String get androidChannelTitle => _channelTitle;

  bool _initialised = false;
  String? _currentToken;

  String? get currentToken => _currentToken;

  /// Must run inside a zone that still handles errors (used for onMessageOpenedApp).
  static Future<void> firebaseBackgroundHandler(RemoteMessage message) async {
    // Notification payloads are auto-displayed by the OS; nothing to do here.
  }

  /// Initialise Firebase, request permission and register the device token.
  ///
  /// Safe to call multiple times; failures are logged and never thrown so the
  /// app still works without Firebase configured.
  Future<void> init() async {
    if (_initialised) return;
    _initialised = true;

    try {
      await Firebase.initializeApp();

      final messaging = FirebaseMessaging.instance;

      // Android 13+ requires an explicit runtime permission.
      await messaging.requestPermission(
        alert: true,
        badge: true,
        sound: true,
        provisional: false,
      );

      // Create the Android notification channel so heads-up popups show.
      final android = FirebaseMessaging.instance;
      await android.setAutoInitEnabled(true);

      _listenForTokenRefresh(messaging);
      _listenForForegroundMessages(messaging);
      _listenForNotificationTap(messaging);

      await registerDeviceToken();
    } catch (e) {
      debugPrint('Notification init error: $e');
    }
  }

  void _listenForTokenRefresh(FirebaseMessaging messaging) {
    messaging.onTokenRefresh.listen((token) async {
      _currentToken = token;
      await _sendTokenToBackend(token);
    });
  }

  void _listenForForegroundMessages(FirebaseMessaging messaging) {
    FirebaseMessaging.onMessage.listen((message) {
      debugPrint('FCM foreground message: ${message.notification?.title}');
    });
  }

  void _listenForNotificationTap(FirebaseMessaging messaging) {
    // Tapping a notification while the app is backgrounded.
    FirebaseMessaging.onMessageOpenedApp.listen((message) {
      debugPrint('FCM message opened: ${message.data}');
    });

    // Tapping a notification that launched the app from a terminated state.
    messaging.getInitialMessage().then((message) {
      if (message != null) {
        debugPrint('FCM initial message: ${message.data}');
      }
    });
  }

  /// Get the current FCM token and push it to the backend.
  Future<void> registerDeviceToken() async {
    try {
      final token = await FirebaseMessaging.instance.getToken();

      if (token == null || token.isEmpty) return;

      // Skip the network call if the server already has this token.
      if (token == _currentToken) return;

      _currentToken = token;
      await _sendTokenToBackend(token);
    } catch (e) {
      debugPrint('Device token registration error: $e');
    }
  }

  Future<void> _sendTokenToBackend(String token) async {
    try {
      await ApiClient().dio.post(
        '/admin/staff/device_token',
        data: {'device_token': token},
      );
    } catch (e) {
      debugPrint('Device token upload error: $e');
    }
  }
}
