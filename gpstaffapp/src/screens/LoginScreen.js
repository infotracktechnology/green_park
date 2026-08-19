import React, { useState, useContext } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Platform,
  Image,
  ScrollView
} from 'react-native';
import { AuthContext } from '../context/AuthContext';
import { Ionicons } from '@expo/vector-icons';

export default function LoginScreen() {
  const { login } = useContext(AuthContext);
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  const handleLogin = async () => {
    if (!username.trim() || !password.trim()) {
      Alert.alert('Required Fields', 'Please enter your username and password.');
      return;
    }
    setSubmitting(true);
    try {
      const res = await login(username.trim(), password);
      console.log(res);
      if (!res.success) {
        Alert.alert('Authentication Failed', res.message || 'Invalid username or password.');
      }
    } catch (e) {
      Alert.alert('Connection Error', 'Unable to reach the server. Please check your internet connection.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      className="flex-1 bg-slate-50"
    >
      <ScrollView contentContainerStyle={{ flexGrow: 1 }} showsVerticalScrollIndicator={false}>
        {/* Top Primary Curved Banner */}
        <View className="bg-primary pt-16 pb-20 px-6 items-center rounded-b-[40px] shadow-lg">
          {/* GPCC Crest Logo Container */}
          <View className="w-28 h-28 bg-white rounded-3xl p-2 items-center justify-center shadow-xl shadow-black/20 border-2 border-white/50">
            <Image
              source={require('../../assets/icon.png')}
              style={{ width: '100%', height: '100%' }}
              resizeMode="contain"
            />
          </View>
          <Text className="text-2xl font-black text-white mt-4 tracking-wide">GPCC PORTAL</Text>
          <Text className="text-xs font-semibold text-blue-100 mt-1">Staff & Management Access</Text>
        </View>

        {/* Login Form Card */}
        <View className="flex-1 px-6 -mt-8">
          <View className="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <Text className="text-lg font-bold text-gray-800 mb-1">Welcome Back</Text>
            <Text className="text-xs text-gray-400 mb-6">Sign in with your admin or staff credentials</Text>

            {/* Username Input */}
            <Text className="text-xs font-bold text-gray-600 uppercase mb-2">Username / ID</Text>
            <View className="bg-slate-50 border border-gray-200 rounded-xl px-4 py-3.5 flex-row items-center mb-4 focus:border-primary">
              <Ionicons name="person" size={18} color="#2b66a2" />
              <TextInput
                placeholder="Enter your username"
                placeholderTextColor="#9ca3af"
                value={username}
                onChangeText={setUsername}
                autoCapitalize="none"
                className="flex-1 ml-3 text-sm text-gray-800 font-medium"
              />
            </View>

            {/* Password Input */}
            <Text className="text-xs font-bold text-gray-600 uppercase mb-2">Password</Text>
            <View className="bg-slate-50 border border-gray-200 rounded-xl px-4 py-3.5 flex-row items-center mb-6 focus:border-primary">
              <Ionicons name="lock-closed" size={18} color="#2b66a2" />
              <TextInput
                placeholder="Enter your password"
                placeholderTextColor="#9ca3af"
                secureTextEntry={!showPassword}
                value={password}
                onChangeText={setPassword}
                className="flex-1 ml-3 text-sm text-gray-800 font-medium"
              />
              <TouchableOpacity onPress={() => setShowPassword(!showPassword)}>
                <Ionicons name={showPassword ? 'eye-off' : 'eye'} size={20} color="#9ca3af" />
              </TouchableOpacity>
            </View>

            {/* Fanta Orange Login Button */}
            <TouchableOpacity
              onPress={handleLogin}
              disabled={submitting}
              className="bg-fanta py-4 rounded-xl items-center shadow-lg shadow-fanta/40 active:opacity-90"
            >
              {submitting ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <View className="flex-row items-center space-x-2">
                  <Text className="text-white font-bold text-base mr-2">Sign In</Text>
                  <Ionicons name="arrow-forward" size={18} color="#fff" />
                </View>
              )}
            </TouchableOpacity>
          </View>

          {/* Footer Copyright */}
          <View className="py-6 items-center">
            <Text className="text-[11px] text-gray-400 font-medium">GPCC Academic Management System</Text>
          </View>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}