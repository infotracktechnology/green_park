import 'package:dio/dio.dart';
import 'package:flutter/material.dart';

import '../api/api_client.dart';
import '../widgets/staff_announcement_form.dart';

/// Admin: edit an existing Staff Announcement.
class EditStaffAnnouncementScreen extends StatelessWidget {
  final dynamic announcementId;

  const EditStaffAnnouncementScreen({super.key, required this.announcementId});

  @override
  Widget build(BuildContext context) {
    return StaffAnnouncementForm(
      announcementId: announcementId,
      submitLabel: 'Update Announcement',
      onSubmit: (FormData formData) async {
        // The form adds the hidden `_method=PUT` field for updates.
        final res = await ApiClient().dio.post(
              '/admin/staffannouncement/$announcementId',
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
