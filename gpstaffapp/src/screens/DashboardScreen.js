import React, { useContext } from 'react';
import { View, Text, TouchableOpacity, ScrollView, Image } from 'react-native';
import { AuthContext } from '../context/AuthContext';
import { Ionicons } from '@expo/vector-icons';

export default function DashboardScreen({ navigation }) {
  const { user, logout } = useContext(AuthContext);

  const role = user?.type?.toLowerCase()?.trim() || 'staff';
  const isAdmin = role === 'admin';
  const isBranchAdmin = role === 'branch admin';

  // Unified menu definitions
  const getMenus = () => {
    if (isAdmin) {
      return [
        { id: 'announcement', title: 'Announcement', icon: 'megaphone', color: '#2b66a2', onPress: () => navigation.navigate('Announcements') },
        { id: 'exams', title: 'Exam Portions', icon: 'document-text', color: '#ff7700', onPress: () => {} },
        { id: 'videos', title: 'Video Classes', icon: 'play-circle', color: '#ff7700', onPress: () => {} },
        { id: 'reports', title: 'Analytics', icon: 'stats-chart', color: '#2b66a2', onPress: () => {} },
      ];
    } else if (isBranchAdmin) {
      return [
        { id: 'branch_announcement', title: 'Announcement', icon: 'megaphone', color: '#2b66a2', onPress: () => navigation.navigate('Announcements') },
        { id: 'staff_overview', title: 'Staff Overview', icon: 'people', color: '#ff7700', onPress: () => {} },
        { id: 'student_directory', title: 'Students', icon: 'school', color: '#ff7700', onPress: () => {} },
        { id: 'branch_reports', title: 'Branch Reports', icon: 'bar-chart', color: '#2b66a2', onPress: () => {} },
      ];
    }
    
    return [
      { id: 'classes', title: 'Class Assign', icon: 'calendar', color: '#2b66a2', onPress: () => {} },
      { id: 'attendance', title: 'Biometric Log', icon: 'finger-print', color: '#ff7700', onPress: () => {} },
      { id: 'announcements', title: 'Branch Notices', icon: 'notifications', color: '#ff7700', onPress: () => {} },
      { id: 'profile', title: 'My Profile', icon: 'person-circle', color: '#2b66a2', onPress: () => {} },
    ];
  };

  const menus = getMenus();
  const title = isAdmin ? 'Administration Modules' : isBranchAdmin ? 'Branch Administration' : 'Staff Dashboard';

  return (
    <ScrollView className="flex-1 bg-slate-50" showsVerticalScrollIndicator={false}>
      {/* Header */}
      <View className="bg-primary pt-14 pb-10 px-6 rounded-b-[36px] shadow-lg">
        <View className="flex-row justify-between items-center">
          <View className="flex-row items-center flex-1 pr-3">
            <View className="w-12 h-12 bg-white rounded-2xl p-1.5 items-center justify-center shadow-sm mr-3">
              <Image source={require('../../assets/icon.png')} style={{ width: '100%', height: '100%' }} resizeMode="contain" />
            </View>
            <View className="flex-1">
              <View className="bg-fanta self-start px-2 py-0.5 rounded-full mb-0.5">
                <Text className="text-[10px] font-black text-white uppercase tracking-wider">{user?.type || 'Staff'}</Text>
              </View>
              <Text className="text-xl font-bold text-white" numberOfLines={1}>
                {user?.username || user?.user_name || 'User'}
              </Text>
            </View>
          </View>
          <TouchableOpacity onPress={logout} className="bg-white/15 p-2.5 rounded-full border border-white/20 active:opacity-80">
            <Ionicons name="log-out-outline" size={20} color="#fff" />
          </TouchableOpacity>
        </View>
      </View>

      {/* Menu Grid */}
      <View className="px-6 pt-8 pb-10">
        <Text className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-5">{title}</Text>
        <View className="flex-row flex-wrap justify-between">
          {menus.map(item => (
            <TouchableOpacity
              key={item.id}
              onPress={item.onPress}
              activeOpacity={0.75}
              className="w-[48%] bg-white p-4 rounded-3xl mb-4 border border-gray-100 shadow-sm justify-between min-h-[145px]"
            >
              <View className="flex-row justify-between items-start">
                <View style={{ backgroundColor: `${item.color}15` }} className="w-12 h-12 rounded-2xl items-center justify-center">
                  <Ionicons name={item.icon} size={24} color={item.color} />
                </View>
              </View>
              <View className="mt-3">
                <Text className="font-bold text-gray-800 text-sm">{item.title}</Text>
                <Text className="text-[11px] text-gray-400 mt-0.5 leading-4" numberOfLines={1}>
                  {item.id === 'announcement' || item.id === 'branch_announcement' ? 'Notices & broadcast' : 'Coming soon'}
                </Text>
              </View>
            </TouchableOpacity>
          ))}
        </View>
      </View>
    </ScrollView>
  );
}