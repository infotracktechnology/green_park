import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class MultiSelectChips<T> extends StatelessWidget {
  final String label;
  final List<T> options;
  final List<dynamic> selected;
  final Function(dynamic value) onToggle;
  final String Function(T item)? labelBuilder;
  final dynamic Function(T item)? valueBuilder;

  const MultiSelectChips({
    super.key,
    required this.label,
    required this.options,
    required this.selected,
    required this.onToggle,
    this.labelBuilder,
    this.valueBuilder,
  });

  @override
  Widget build(BuildContext context) {
    if (options.isEmpty) return const SizedBox.shrink();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label.toUpperCase(),
          style: const TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.bold,
            color: AppColors.textSecondary,
            letterSpacing: 0.5,
          ),
        ),
        const SizedBox(height: 8),
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          physics: const BouncingScrollPhysics(),
          child: Row(
            children: options.map((item) {
              final val = valueBuilder != null ? valueBuilder!(item) : item;
              final text =
                  labelBuilder != null ? labelBuilder!(item) : item.toString();
              final isSelected = selected.contains(val);

              return Padding(
                padding: const EdgeInsets.only(right: 8),
                child: Material(
                  color: isSelected ? AppColors.primary : Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  child: InkWell(
                    onTap: () => onToggle(val),
                    borderRadius: BorderRadius.circular(20),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 8),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(
                          color:
                              isSelected ? AppColors.primary : AppColors.border,
                          width: 1,
                        ),
                      ),
                      child: Text(
                        text,
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color:
                              isSelected ? Colors.white : AppColors.textPrimary,
                        ),
                      ),
                    ),
                  ),
                ),
              );
            }).toList(),
          ),
        ),
        const SizedBox(height: 16),
      ],
    );
  }
}
