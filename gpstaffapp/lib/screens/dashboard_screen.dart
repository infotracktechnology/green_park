import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';
import 'announcement_list_screen.dart';
import 'chairman_video_list_screen.dart';
import 'exam_portion_list_screen.dart';
import 'question_key_list_screen.dart';
import 'answer_key_list_screen.dart';

class MenuItemModel {
  final String id;
  final String title;
  final String subtitle;
  final IconData icon;
  final Color color;
  final VoidCallback? onTap;

  MenuItemModel({
    required this.id,
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.color,
    this.onTap,
  });
}

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    final user = auth.user;
    final role = (user?.type ?? 'staff').toLowerCase().trim();
    final isAdmin = role == 'admin';
    final isBranchAdmin = role == 'branch admin';

    List<MenuItemModel> getMenus() {
      if (isAdmin) {
        return [
          MenuItemModel(
            id: 'announcement',
            title: 'Announcement',
            subtitle: 'Notices & broadcast',
            icon: Icons.campaign,
            color: AppColors.primary,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const AnnouncementListScreen()),
            ),
          ),
          MenuItemModel(
            id: 'chairman_video',
            title: 'Chairman Video',
            subtitle: 'Video broadcasts',
            icon: Icons.videocam_outlined,
            color: AppColors.fanta,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                  builder: (_) => const ChairmanVideoListScreen()),
            ),
          ),
          MenuItemModel(
            id: 'exam_portion',
            title: 'Exam Portions',
            subtitle: 'Syllabus & portions',
            icon: Icons.description_outlined,
            color: AppColors.fanta,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const ExamPortionListScreen()),
            ),
          ),
          MenuItemModel(
            id: 'question_paper',
            title: 'Question Papers',
            subtitle: 'Manage papers',
            icon: Icons.quiz_outlined,
            color: AppColors.fanta,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const QuestionKeyListScreen()),
            ),
          ),
          MenuItemModel(
            id: 'answer_key',
            title: 'Answer Keys',
            subtitle: 'Keys & solutions',
            icon: Icons.fact_check_outlined,
            color: AppColors.primary,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const AnswerKeyListScreen()),
            ),
          ),
        ];
      } else if (isBranchAdmin) {
        return [
          MenuItemModel(
            id: 'student_directory',
            title: 'Students',
            subtitle: 'Coming soon',
            icon: Icons.school_outlined,
            color: AppColors.fanta,
          ),
          MenuItemModel(
            id: 'branch_reports',
            title: 'Branch Reports',
            subtitle: 'Coming soon',
            icon: Icons.bar_chart,
            color: AppColors.primary,
          ),
        ];
      }

      return [
        MenuItemModel(
          id: 'profile',
          title: 'My Profile',
          subtitle: 'Coming soon',
          icon: Icons.account_circle_outlined,
          color: AppColors.primary,
        ),
      ];
    }

    final menus = getMenus();
    final headerTitle = isAdmin
        ? 'Administration Modules'
        : isBranchAdmin
            ? 'Branch Administration'
            : 'Staff Dashboard';

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            Container(
              padding: const EdgeInsets.only(
                  top: 56, bottom: 36, left: 20, right: 20),
              decoration: const BoxDecoration(
                color: AppColors.primary,
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(36),
                  bottomRight: Radius.circular(36),
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black12,
                    blurRadius: 10,
                    offset: Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                children: [
                  // Logo container
                  Container(
                    width: 48,
                    height: 48,
                    padding: const EdgeInsets.all(6),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.1),
                          blurRadius: 8,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Image.asset(
                      'assets/icon.png',
                      fit: BoxFit.contain,
                      errorBuilder: (_, __, ___) =>
                          const Icon(Icons.school, color: AppColors.primary),
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(
                            color: AppColors.fanta,
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(
                            (user?.type ?? 'Staff').toUpperCase(),
                            style: const TextStyle(
                              fontSize: 9,
                              fontWeight: FontWeight.w900,
                              color: Colors.white,
                              letterSpacing: 0.8,
                            ),
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          user?.username ?? 'User',
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                  // Logout Button
                  IconButton(
                    onPressed: () {
                      showDialog(
                        context: context,
                        builder: (ctx) => AlertDialog(
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(20)),
                          title: const Text('Sign Out',
                              style: TextStyle(fontWeight: FontWeight.bold)),
                          content: const Text(
                              'Are you sure you want to log out from GPCC Portal?'),
                          actions: [
                            TextButton(
                              onPressed: () => Navigator.pop(ctx),
                              child: const Text('Cancel',
                                  style: TextStyle(
                                      color: AppColors.textSecondary)),
                            ),
                            TextButton(
                              onPressed: () async {
                                Navigator.pop(ctx);
                                await auth.logout();
                                if (context.mounted) {
                                  Navigator.of(context).pushNamedAndRemoveUntil(
                                      '/login', (route) => false);
                                }
                              },
                              child: const Text('Logout',
                                  style: TextStyle(
                                      color: AppColors.error,
                                      fontWeight: FontWeight.bold)),
                            ),
                          ],
                        ),
                      );
                    },
                    icon:
                        const Icon(Icons.logout, color: Colors.white, size: 20),
                    style: IconButton.styleFrom(
                      backgroundColor: Colors.white.withOpacity(0.15),
                      padding: const EdgeInsets.all(10),
                    ),
                  ),
                ],
              ),
            ),

            // Modules Grid
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 28, 20, 36),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    headerTitle.toUpperCase(),
                    style: const TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textSecondary,
                      letterSpacing: 0.8,
                    ),
                  ),
                  const SizedBox(height: 16),
                  GridView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    gridDelegate:
                        const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      crossAxisSpacing: 14,
                      mainAxisSpacing: 14,
                      childAspectRatio: 1.1,
                    ),
                    itemCount: menus.length,
                    itemBuilder: (context, index) {
                      final item = menus[index];
                      return InkWell(
                        onTap: item.onTap,
                        borderRadius: BorderRadius.circular(24),
                        child: Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(24),
                            border: Border.all(color: AppColors.borderLight),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.02),
                                blurRadius: 8,
                                offset: const Offset(0, 2),
                              ),
                            ],
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Container(
                                width: 44,
                                height: 44,
                                decoration: BoxDecoration(
                                  color: item.color.withOpacity(0.12),
                                  borderRadius: BorderRadius.circular(14),
                                ),
                                child: Icon(item.icon,
                                    color: item.color, size: 24),
                              ),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    item.title,
                                    style: const TextStyle(
                                      fontSize: 14,
                                      fontWeight: FontWeight.bold,
                                      color: AppColors.textPrimary,
                                    ),
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    item.subtitle,
                                    style: const TextStyle(
                                      fontSize: 11,
                                      color: AppColors.textMuted,
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
