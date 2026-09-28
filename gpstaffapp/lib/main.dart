import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

import 'providers/auth_provider.dart';
import 'providers/announcement_filter_provider.dart';
import 'theme/app_theme.dart';
import 'screens/splash_screen.dart';
import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';
import 'screens/announcement_list_screen.dart';
import 'screens/create_announcement_screen.dart';
import 'screens/staff_announcement_list_screen.dart';
import 'screens/create_staff_announcement_screen.dart';
import 'screens/chairman_video_list_screen.dart';
import 'screens/create_chairman_video_screen.dart';
import 'screens/download_list_screen.dart';
import 'screens/create_download_screen.dart';
import 'services/notification_service.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();

  // FCM background handler must be registered before Firebase initialises.
  FirebaseMessaging.onBackgroundMessage(
      NotificationService.firebaseBackgroundHandler);

  runApp(const GPStaffApp());
}

class GPStaffApp extends StatelessWidget {
  const GPStaffApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => AnnouncementFilterProvider()),
      ],
      child: MaterialApp(
        title: 'GPCC Staff Portal',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.lightTheme,
        home: const SplashScreen(),
        routes: {
          '/splash': (_) => const SplashScreen(),
          '/login': (_) => const LoginScreen(),
          '/dashboard': (_) => const DashboardScreen(),
          '/announcements': (_) => const AnnouncementListScreen(),
          '/create_announcement': (_) => const CreateAnnouncementScreen(),
          '/staff_announcements': (_) => const StaffAnnouncementListScreen(),
          '/create_staff_announcement': (_) =>
              const CreateStaffAnnouncementScreen(),
          '/chairman_videos': (_) => const ChairmanVideoListScreen(),
          '/create_chairman_video': (_) => const CreateChairmanVideoScreen(),
          '/downloads': (_) => const DownloadListScreen(),
          '/create_download': (_) => const CreateDownloadScreen(),
        },
      ),
    );
  }
}
