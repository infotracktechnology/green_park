import React, { useState, useEffect } from 'react';
import { View, Text, FlatList, TouchableOpacity, RefreshControl } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import API from '../api/client';

export default function AnnouncementListScreen({ navigation }) {
  const [announcements, setAnnouncements] = useState([]);
  const [loading, setLoading] = useState(false);
  const [filterType, setFilterType] = useState('');

  const coachingTypes = ['ALL', 'OFFLINE', 'ONLINE', 'TEST BATCH'];

  useEffect(() => {
    fetchAnnouncements();
  }, [filterType]);

  const fetchAnnouncements = async () => {
    setLoading(true);
    try {
      const endpoint = filterType && filterType !== 'ALL'
        ? `/admin/announcement?coaching_type=${filterType}`
        : '/admin/announcement';
      const res = await API.get(endpoint);
      if (res.data.status) {
        setAnnouncements(res.data.announcements || []);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  return (
    <View className="flex-1 bg-slate-50">
      {/* Filter Tabs */}
      <View className="bg-white py-3 px-4 border-b border-gray-100 shadow-sm">
        <FlatList
          horizontal
          showsHorizontalScrollIndicator={false}
          data={coachingTypes}
          keyExtractor={(item) => item}
          renderItem={({ item }) => {
            const active = filterType === item || (item === 'ALL' && !filterType);
            return (
              <TouchableOpacity
                onPress={() => setFilterType(item === 'ALL' ? '' : item)}
                className={`mr-2 px-4 py-1.5 rounded-full border ${
                  active ? 'bg-primary border-primary' : 'bg-gray-50 border-gray-200'
                }`}
              >
                <Text className={`text-xs font-bold ${active ? 'text-white' : 'text-gray-600'}`}>
                  {item}
                </Text>
              </TouchableOpacity>
            );
          }}
        />
      </View>

      {/* Announcements List */}
      <FlatList
        data={announcements}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={{ padding: 16, paddingBottom: 90 }}
        refreshControl={
          <RefreshControl refreshing={loading} onRefresh={fetchAnnouncements} tintColor="#ff7700" />
        }
        ListEmptyComponent={
          !loading && (
            <View className="items-center justify-center py-20">
              <View className="w-16 h-16 rounded-full bg-blue-50 items-center justify-center mb-3">
                <Ionicons name="megaphone-outline" size={32} color="#2b66a2" />
              </View>
              <Text className="text-gray-600 font-bold text-base">No Announcements</Text>
              <Text className="text-gray-400 text-xs mt-1">Tap + to add a new announcement</Text>
            </View>
          )
        }
        renderItem={({ item }) => (
          <TouchableOpacity
            onPress={() => navigation.navigate('EditAnnouncement', { announcementId: item.id })}
            activeOpacity={0.8}
            className="bg-white rounded-2xl p-4 mb-3 border border-gray-100 shadow-sm"
          >
            <View className="flex-row justify-between items-center mb-2.5">
              <View className="flex-row flex-wrap gap-1.5">
                <View className="bg-primary/10 px-2.5 py-0.5 rounded-md">
                  <Text className="text-[11px] font-bold text-primary">{item.usertype}</Text>
                </View>
                <View className="bg-fanta/10 px-2.5 py-0.5 rounded-md">
                  <Text className="text-[11px] font-bold text-fanta">{item.course || 'All'}</Text>
                </View>
                {item.is_schedule == 1 && (
                  <View className="bg-amber-100 px-2 py-0.5 rounded-md">
                    <Text className="text-[10px] font-bold text-amber-700">SCHEDULED</Text>
                  </View>
                )}
              </View>
              <Text className="text-[11px] text-gray-400 font-medium">
                {item.created_at ? new Date(item.created_at).toLocaleDateString() : ''}
              </Text>
            </View>

            <Text className="text-base font-bold text-gray-800 mb-1.5">{item.title}</Text>
            <Text className="text-xs text-gray-500 leading-5" numberOfLines={3}>
              {item.content?.replace(/<[^>]*>?/gm, '')}
            </Text>

            <View className="flex-row items-center justify-between mt-3 pt-3 border-t border-gray-50">
              <View className="flex-row items-center flex-1 mr-2">
                <Ionicons name="people-outline" size={14} color="#9ca3af" />
                <Text className="text-[11px] text-gray-400 font-medium ml-1.5" numberOfLines={1}>
                  {item.coaching_type || 'ALL'} • {item.category || 'All Categories'}
                </Text>
              </View>
              <View className="flex-row items-center">
                <Text className="text-xs font-bold text-primary mr-1">Edit</Text>
                <Ionicons name="chevron-forward" size={14} color="#2b66a2" />
              </View>
            </View>
          </TouchableOpacity>
        )}
      />

      {/* Floating Action Button */}
      <TouchableOpacity
        onPress={() => navigation.navigate('CreateAnnouncement')}
        className="absolute bottom-6 right-6 bg-fanta w-14 h-14 rounded-full items-center justify-center shadow-lg shadow-fanta/50 active:scale-95"
      >
        <Ionicons name="add" size={30} color="#fff" />
      </TouchableOpacity>
    </View>
  );
}