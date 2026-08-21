import 'dart:async';
import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../models/student_model.dart';

class StudentSelector extends StatefulWidget {
  final String selectedStudentId;
  final Function(String studentId) onSelectStudent;
  final Map<String, String> studentOptions;
  final bool loading;
  final Function(String query)? onSearch;
  final String searchValue;
  final Function(String value)? onSearchChanged;
  final List<String> sectionOptions;
  final String selectedSection;
  final Function(String section)? onSelectSection;

  const StudentSelector({
    super.key,
    required this.selectedStudentId,
    required this.onSelectStudent,
    this.studentOptions = const {},
    this.loading = false,
    this.onSearch,
    this.searchValue = '',
    this.onSearchChanged,
    this.sectionOptions = const [],
    this.selectedSection = '',
    this.onSelectSection,
  });

  @override
  State<StudentSelector> createState() => _StudentSelectorState();
}

class _StudentSelectorState extends State<StudentSelector> {
  late bool _isExpanded;
  late TextEditingController _searchController;
  Timer? _debounceTimer;

  @override
  void initState() {
    super.initState();
    _isExpanded = widget.selectedStudentId.isEmpty;
    _searchController = TextEditingController(text: widget.searchValue);
  }

  @override
  void didUpdateWidget(covariant StudentSelector oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.selectedStudentId != widget.selectedStudentId && widget.selectedStudentId.isNotEmpty) {
      _isExpanded = false;
    }
    if (oldWidget.searchValue != widget.searchValue && widget.searchValue != _searchController.text) {
      _searchController.text = widget.searchValue;
    }
  }

  @override
  void dispose() {
    _debounceTimer?.cancel();
    _searchController.dispose();
    super.dispose();
  }

  void _onSearchInput(String text) {
    setState(() {});
    widget.onSearchChanged?.call(text);

    _debounceTimer?.cancel();
    _debounceTimer = Timer(const Duration(milliseconds: 350), () {
      widget.onSearch?.call(text);
    });
  }

  List<StudentItem> get _filteredStudents {
    final query = _searchController.text.trim().toLowerCase();
    final all = widget.studentOptions.entries
        .map((e) => StudentItem(id: e.key, name: e.value))
        .toList();

    if (query.isEmpty) return all;
    return all.where((s) {
      final idMatch = s.id.toLowerCase().contains(query);
      final nameMatch = s.name.toLowerCase().contains(query);
      return idMatch || nameMatch;
    }).toList();
  }

  String get _selectedStudentName {
    if (widget.selectedStudentId.isEmpty) return '';
    return widget.studentOptions[widget.selectedStudentId] ?? widget.selectedStudentId;
  }

  @override
  Widget build(BuildContext context) {
    final students = _filteredStudents;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'TARGET STUDENT *',
          style: TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.bold,
            color: AppColors.textSecondary,
            letterSpacing: 0.5,
          ),
        ),
        const SizedBox(height: 8),

        if (widget.selectedStudentId.isNotEmpty && !_isExpanded)
          // Collapsed Selected Card
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: AppColors.primary.withOpacity(0.06),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.primary.withOpacity(0.25)),
            ),
            child: Row(
              children: [
                Container(
                  width: 42,
                  height: 42,
                  decoration: BoxDecoration(
                    color: AppColors.primary.withOpacity(0.12),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.person, color: AppColors.primary, size: 22),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'ID: ${widget.selectedStudentId}',
                        style: const TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: AppColors.primary,
                        ),
                      ),
                      Text(
                        _selectedStudentName,
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: AppColors.textPrimary,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                // Action Buttons
                TextButton(
                  onPressed: () => setState(() => _isExpanded = true),
                  style: TextButton.styleFrom(
                    backgroundColor: AppColors.primary.withOpacity(0.1),
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    minimumSize: Size.zero,
                  ),
                  child: const Text(
                    'Change',
                    style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppColors.primary),
                  ),
                ),
                const SizedBox(width: 6),
                IconButton(
                  onPressed: () {
                    widget.onSelectStudent('');
                    setState(() => _isExpanded = true);
                  },
                  icon: const Icon(Icons.close, color: AppColors.error, size: 18),
                  style: IconButton.styleFrom(
                    backgroundColor: AppColors.error.withOpacity(0.08),
                    padding: const EdgeInsets.all(6),
                    minimumSize: Size.zero,
                  ),
                ),
              ],
            ),
          )
        else
          // Expanded Search & Filter Card
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: const Color(0xFFF8FAFC),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: AppColors.border),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Header with Count Badge
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.search, size: 16, color: AppColors.primary),
                        const SizedBox(width: 6),
                        const Text(
                          'Search & Filter Students',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: AppColors.textPrimary,
                          ),
                        ),
                        if (!widget.loading && students.isNotEmpty) ...[
                          const SizedBox(width: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                            decoration: BoxDecoration(
                              color: AppColors.primary.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Text(
                              '${students.length}',
                              style: const TextStyle(
                                fontSize: 10,
                                fontWeight: FontWeight.bold,
                                color: AppColors.primary,
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                    if (widget.selectedStudentId.isNotEmpty)
                      GestureDetector(
                        onTap: () => setState(() => _isExpanded = false),
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: Colors.grey.shade200,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: const Text(
                            'Done',
                            style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.textSecondary),
                          ),
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 12),

                // Search Input Field
                Container(
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: TextField(
                    controller: _searchController,
                    onChanged: _onSearchInput,
                    textInputAction: TextInputAction.search,
                    onSubmitted: (val) {
                      _debounceTimer?.cancel();
                      widget.onSearch?.call(val);
                    },
                    style: const TextStyle(fontSize: 13, color: AppColors.textPrimary),
                    decoration: InputDecoration(
                      hintText: 'Search by student name or ID...',
                      hintStyle: const TextStyle(fontSize: 12, color: AppColors.textMuted),
                      prefixIcon: const Icon(Icons.search, size: 18, color: AppColors.primary),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.cancel, size: 16, color: AppColors.textMuted),
                              onPressed: () {
                                _searchController.clear();
                                _onSearchInput('');
                              },
                            )
                          : null,
                      border: InputBorder.none,
                      enabledBorder: InputBorder.none,
                      focusedBorder: InputBorder.none,
                      contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                    ),
                  ),
                ),
                const SizedBox(height: 10),

                // Section Filter Chips
                if (widget.sectionOptions.isNotEmpty) ...[
                  SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    physics: const BouncingScrollPhysics(),
                    child: Row(
                      children: [
                        Padding(
                          padding: const EdgeInsets.only(right: 6),
                          child: ChoiceChip(
                            label: const Text('All Sections'),
                            labelStyle: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: widget.selectedSection.isEmpty ? Colors.white : AppColors.textSecondary,
                            ),
                            selected: widget.selectedSection.isEmpty,
                            selectedColor: AppColors.primary,
                            backgroundColor: Colors.white,
                            side: BorderSide(
                              color: widget.selectedSection.isEmpty ? AppColors.primary : AppColors.border,
                            ),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            onSelected: (_) => widget.onSelectSection?.call(''),
                          ),
                        ),
                        ...widget.sectionOptions.map(
                          (sec) => Padding(
                            padding: const EdgeInsets.only(right: 6),
                            child: ChoiceChip(
                              label: Text(sec),
                              labelStyle: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: widget.selectedSection == sec ? Colors.white : AppColors.textSecondary,
                              ),
                              selected: widget.selectedSection == sec,
                              selectedColor: AppColors.primary,
                              backgroundColor: Colors.white,
                              side: BorderSide(
                                color: widget.selectedSection == sec ? AppColors.primary : AppColors.border,
                              ),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                              onSelected: (_) => widget.onSelectSection?.call(sec),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 10),
                ],

                // Student List
                if (widget.loading)
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 24),
                    child: Center(
                      child: Column(
                        children: [
                          SizedBox(
                            width: 24,
                            height: 24,
                            child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.fanta),
                          ),
                          SizedBox(height: 8),
                          Text(
                            'Loading students...',
                            style: TextStyle(fontSize: 11, color: AppColors.textMuted),
                          ),
                        ],
                      ),
                    ),
                  )
                else if (students.isNotEmpty)
                  Container(
                    constraints: const BoxConstraints(maxHeight: 220),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: AppColors.borderLight),
                    ),
                    child: ListView.separated(
                      shrinkWrap: true,
                      padding: const EdgeInsets.all(6),
                      itemCount: students.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 4),
                      itemBuilder: (context, index) {
                        final item = students[index];
                        final isSelected = widget.selectedStudentId == item.id;

                        return InkWell(
                          onTap: () {
                            widget.onSelectStudent(item.id);
                            setState(() => _isExpanded = false);
                          },
                          borderRadius: BorderRadius.circular(10),
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                            decoration: BoxDecoration(
                              color: isSelected ? AppColors.primary.withOpacity(0.08) : Colors.white,
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(
                                color: isSelected ? AppColors.primary.withOpacity(0.3) : AppColors.borderLight,
                              ),
                            ),
                            child: Row(
                              children: [
                                Container(
                                  width: 32,
                                  height: 32,
                                  decoration: BoxDecoration(
                                    color: isSelected ? AppColors.primary : Colors.grey.shade100,
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Icon(
                                    Icons.person,
                                    size: 16,
                                    color: isSelected ? Colors.white : AppColors.textSecondary,
                                  ),
                                ),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        item.name,
                                        style: TextStyle(
                                          fontSize: 12,
                                          fontWeight: FontWeight.bold,
                                          color: isSelected ? AppColors.primary : AppColors.textPrimary,
                                        ),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                      Text(
                                        'ID: ${item.id}',
                                        style: const TextStyle(fontSize: 10, color: AppColors.textMuted),
                                      ),
                                    ],
                                  ),
                                ),
                                if (isSelected)
                                  const Icon(Icons.check_circle, size: 18, color: AppColors.primary)
                                else
                                  const Icon(Icons.chevron_right, size: 16, color: AppColors.textMuted),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                  )
                else
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 20),
                    child: Center(
                      child: Column(
                        children: [
                          Icon(Icons.person_search_outlined, size: 28, color: AppColors.textMuted),
                          SizedBox(height: 6),
                          Text(
                            'No matching students',
                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppColors.textSecondary),
                          ),
                          SizedBox(height: 2),
                          Text(
                            'Try searching with a different name or ID',
                            style: TextStyle(fontSize: 11, color: AppColors.textMuted),
                          ),
                        ],
                      ),
                    ),
                  ),
              ],
            ),
          ),
        const SizedBox(height: 16),
      ],
    );
  }
}
