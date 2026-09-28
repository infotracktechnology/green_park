import 'package:dio/dio.dart';
import 'package:flutter/material.dart';

import '../api/api_client.dart';
import '../widgets/staff_announcement_form.dart';

/// Admin: create a new Staff Announcement.
class CreateStaffAnnouncementScreen extends StatelessWidget {
  const CreateStaffAnnouncementScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return StaffAnnouncementForm(
      submitLabel: 'Publish Announcement',
      onSubmit: (FormData formData) async {
        final res = await ApiClient().dio.post(
              '/admin/staffannouncement',
              data: formData,
              options: Options(contentType: 'multipart/form-data'),
            );

        return res.data is Map
            ? Map<String, dynamic>.from(res.data as Map)
            : null;
      },
    );
  }
}
