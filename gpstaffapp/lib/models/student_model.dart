class StudentItem {
  final String id;
  final String name;

  StudentItem({required this.id, required this.name});

  factory StudentItem.fromDynamic(dynamic idKey, dynamic value) {
    if (value is Map<String, dynamic>) {
      return StudentItem(
        id: (value['student_id'] ?? value['id'] ?? idKey ?? '').toString(),
        name:
            (value['student_name'] ?? value['name'] ?? idKey ?? '').toString(),
      );
    }
    return StudentItem(
      id: idKey.toString(),
      name: (value ?? idKey).toString(),
    );
  }
}
