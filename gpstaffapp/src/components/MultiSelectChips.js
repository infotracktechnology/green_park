import React from 'react';
import { View, Text, TouchableOpacity, ScrollView } from 'react-native';

export default function MultiSelectChips({ label, options, selected = [], onToggle, keyField = 'id', labelField = 'name' }) {
  return (
    <View className="mb-4">
      <Text className="text-xs font-bold text-gray-600 mb-2 uppercase">{label}</Text>
      <ScrollView horizontal showsHorizontalScrollIndicator={false} className="flex-row">
        {options.map((opt, idx) => {
          const val = typeof opt === 'object' ? opt[keyField] : opt;
          const display = typeof opt === 'object' ? opt[labelField] : opt;
          const isSelected = selected.includes(val);

          return (
            <TouchableOpacity
              key={idx}
              onPress={() => onToggle(val)}
              className={`mr-2 px-3 py-1.5 rounded-full border ${
                isSelected
                  ? 'bg-primary border-primary'
                  : 'bg-white border-gray-300'
              }`}
            >
              <Text className={`text-xs font-semibold ${isSelected ? 'text-white' : 'text-gray-700'}`}>
                {display}
              </Text>
            </TouchableOpacity>
          );
        })}
      </ScrollView>
    </View>
  );
}