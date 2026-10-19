<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserData($userId) {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: /studentlance/auth/login.php');
        exit();
    }
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function getLanguage() {
    if (isset($_SESSION['lang'])) {
        return $_SESSION['lang'];
    }
    return 'ar';
}

function __($key) {
    static $translations = null;
    if ($translations === null) {
        $translations = [
            'site_title' => ['ar' => 'منصة العمل الحر للطلاب', 'en' => 'Student Freelance Platform'],
            'home' => ['ar' => 'الرئيسية', 'en' => 'Home'],
            'browse_projects' => ['ar' => 'تصفح الطلبات', 'en' => 'Browse Requests'],
            'find_freelancers' => ['ar' => 'تصفح المستقلين', 'en' => 'Find Freelancers'],
            'login' => ['ar' => 'تسجيل الدخول', 'en' => 'Login'],
            'register' => ['ar' => 'إنشاء حساب', 'en' => 'Sign Up'],
            'logout' => ['ar' => 'تسجيل الخروج', 'en' => 'Logout'],
            'dashboard' => ['ar' => 'لوحة التحكم', 'en' => 'Dashboard'],
            'language_toggle' => ['ar' => 'English', 'en' => 'العربية'],
            'details' => ['ar' => 'تفاصيل', 'en' => 'Details'],
            'profile' => ['ar' => 'الملف الشخصي', 'en' => 'Profile'],
            'about' => ['ar' => 'عن المنصة', 'en' => 'About Us'],
            'contact' => ['ar' => 'اتصل بنا', 'en' => 'Contact Us'],
            'privacy' => ['ar' => 'سياسة الخصوصية', 'en' => 'Privacy Policy'],
            'search' => ['ar' => 'بحث', 'en' => 'Search'],
            'no_results' => ['ar' => 'لا توجد نتائج', 'en' => 'No results found'],
            'apply_now' => ['ar' => 'تقديم عرض', 'en' => 'Apply Now'],
            'view_details' => ['ar' => 'عرض التفاصيل', 'en' => 'View Details'],
            'posted_by' => ['ar' => 'بواسطة', 'en' => 'Posted by'],
            'budget' => ['ar' => 'الميزانية', 'en' => 'Budget'],
            'category' => ['ar' => 'التصنيف', 'en' => 'Category'],
            'skills' => ['ar' => 'المهارات', 'en' => 'Skills'],
            'hourly_rate' => ['ar' => 'أجر الساعة', 'en' => 'Hourly Rate'],
            'rating' => ['ar' => 'التقييم', 'en' => 'Rating'],
            'completed_projects' => ['ar' => 'المشاريع المكتملة', 'en' => 'Completed Projects'],
            'total_earnings' => ['ar' => 'إجمالي الأرباح', 'en' => 'Total Earnings'],
            'active_bids' => ['ar' => 'العطاءات النشطة', 'en' => 'Active Bids'],

            // Hero
            'hero_title' => ['ar' => 'منصتك للخدمات الطلابية', 'en' => 'Your Student Services Platform'],
            'hero_text' => ['ar' => 'كطالب: اطلب أي خدمة تحتاجها. كمستقل: اعرض مهاراتك واربح.', 'en' => 'As a student: request any service you need. As a freelancer: offer your skills and earn.'],
            'start_earning' => ['ar' => 'أريد تقديم خدمة', 'en' => 'I want to offer a service'],
            'hire_talent' => ['ar' => 'أحتاج خدمة', 'en' => 'I need a service'],

            'active_students' => ['ar' => 'طلبات مفتوحة', 'en' => 'Open Requests'],
            'projects_completed' => ['ar' => 'خدمات مكتملة', 'en' => 'Completed Services'],
            'student_earnings' => ['ar' => 'أرباح المستقلين', 'en' => 'Freelancer Earnings'],
            'avg_rating' => ['ar' => 'متوسط التقييم', 'en' => 'Average Rating'],

            // How it works
            'how_it_works' => ['ar' => 'كيف تعمل المنصة', 'en' => 'How It Works'],
            'how_subtitle' => ['ar' => 'سواء كنت بحاجة لخدمة أو تقدمها، ابدأ بثلاث خطوات', 'en' => 'Whether you need a service or offer one, start in three steps'],
            'step1_title' => ['ar' => 'أنشئ حسابك', 'en' => 'Create Your Account'],
            'step1_text' => ['ar' => 'اختر دورك: طالب تبحث عن خدمة، أو مستقل تقدم مهاراتك.', 'en' => 'Choose your role: student looking for a service, or freelancer offering your skills.'],
            'step2_title' => ['ar' => 'انشر أو تصفح', 'en' => 'Post or Browse'],
            'step2_text' => ['ar' => 'انشر طلبك أو تصفح المشاريع المتاحة وقدّم عرضاً.', 'en' => 'Post your request or browse available projects and submit a proposal.'],
            'step3_title' => ['ar' => 'سلّم واستلم', 'en' => 'Deliver & Get Paid'],
            'step3_text' => ['ar' => 'سلّم العمل عالي الجودة واستلم أجرك بأمان.', 'en' => 'Deliver quality work and receive your payment securely.'],

            'popular_categories' => ['ar' => 'التصنيفات الشائعة', 'en' => 'Popular Categories'],
            'categories_subtitle' => ['ar' => 'استعرض الخدمات المطلوبة', 'en' => 'Browse requested services'],
            'featured_projects' => ['ar' => 'أحدث الطلبات', 'en' => 'Latest Requests'],
            'featured_subtitle' => ['ar' => 'طلبات خدمة من زملائك الطلاب', 'en' => 'Service requests from fellow students'],

            // Student dashboard (يبحث عن خدمة)
            'student_dashboard' => ['ar' => 'لوحة تحكم الطالب (أحتاج خدمة)', 'en' => 'Student Dashboard (I need a service)'],
            'welcome_student' => ['ar' => 'مرحباً', 'en' => 'Welcome'],
            'my_requests' => ['ar' => 'طلباتي', 'en' => 'My Requests'],
            'no_requests' => ['ar' => 'لم تنشر أي طلب بعد.', 'en' => 'No requests posted yet.'],
            'post_request' => ['ar' => 'انشر طلباً جديداً', 'en' => 'Post a New Request'],
            'request_title' => ['ar' => 'عنوان الطلب', 'en' => 'Request Title'],
            'request_description' => ['ar' => 'وصف الخدمة المطلوبة', 'en' => 'Service Description'],
            'publish' => ['ar' => 'نشر', 'en' => 'Publish'],
            'success_posted' => ['ar' => 'تم نشر طلبك بنجاح!', 'en' => 'Your request has been posted!'],

            // Client dashboard (يقدم خدمة)
            'client_dashboard' => ['ar' => 'لوحة تحكم المستقل (أقدم خدمة)', 'en' => 'Freelancer Dashboard (I offer a service)'],
            'your_earnings' => ['ar' => 'أرباحك', 'en' => 'Your Earnings'],
            'your_rating' => ['ar' => 'تقييمك', 'en' => 'Your Rating'],
            'your_completed' => ['ar' => 'خدمات مكتملة', 'en' => 'Completed Services'],
            'your_bids' => ['ar' => 'عروضك النشطة', 'en' => 'Your Active Bids'],
            'find_projects' => ['ar' => 'تصفح الطلبات', 'en' => 'Browse Requests'],
            'no_bids' => ['ar' => 'لا توجد عروض بعد.', 'en' => 'No bids yet.'],
            'pending' => ['ar' => 'قيد الانتظار', 'en' => 'Pending'],
            'accepted' => ['ar' => 'مقبول', 'en' => 'Accepted'],
            'rejected' => ['ar' => 'مرفوض', 'en' => 'Rejected'],

            // Projects & Freelancers
            'available_projects' => ['ar' => 'الطلبات المتاحة', 'en' => 'Available Requests'],
            'search_projects' => ['ar' => 'ابحث في الطلبات...', 'en' => 'Search requests...'],
            'all_categories' => ['ar' => 'كل التصنيفات', 'en' => 'All Categories'],
            'freelancers_title' => ['ar' => 'المستقلون', 'en' => 'Freelancers'],
            'search_freelancers' => ['ar' => 'ابحث عن مستقل...', 'en' => 'Search freelancers...'],
            'no_projects_available' => ['ar' => 'لا توجد طلبات حالياً.', 'en' => 'No requests at the moment.'],
            'no_freelancers' => ['ar' => 'لا يوجد مستقلين حالياً.', 'en' => 'No freelancers at the moment.'],

            // Project details
            'project_details' => ['ar' => 'تفاصيل الطلب', 'en' => 'Request Details'],
            'submit_proposal' => ['ar' => 'تقديم عرض', 'en' => 'Submit Proposal'],
            'bid_amount' => ['ar' => 'المبلغ المطلوب ($)', 'en' => 'Your Bid ($)'],
            'proposal_text' => ['ar' => 'خطاب العرض', 'en' => 'Proposal Letter'],
            'login_to_apply' => ['ar' => 'سجل الدخول كـ "أقدم خدمة" لتتمكن من التقديم', 'en' => 'Login as "I offer a service" to apply'],

            // Auth
            'login_title' => ['ar' => 'تسجيل الدخول', 'en' => 'Login'],
            'login_welcome' => ['ar' => 'مرحباً بعودتك إلى StudentLance', 'en' => 'Welcome back to StudentLance'],
            'email' => ['ar' => 'البريد الإلكتروني', 'en' => 'Email'],
            'password' => ['ar' => 'كلمة المرور', 'en' => 'Password'],
            'login_btn' => ['ar' => 'دخول', 'en' => 'Login'],
            'no_account' => ['ar' => 'ليس لديك حساب؟', 'en' => 'Don\'t have an account?'],
            'create_account' => ['ar' => 'إنشاء حساب جديد', 'en' => 'Create an Account'],
            'register_title' => ['ar' => 'إنشاء حساب جديد', 'en' => 'Register'],
            'register_subtitle' => ['ar' => 'انضم إلى StudentLance وابدأ رحلتك', 'en' => 'Join StudentLance and start your journey'],
            'full_name' => ['ar' => 'الاسم الكامل', 'en' => 'Full Name'],
            'username' => ['ar' => 'اسم المستخدم', 'en' => 'Username'],
            'confirm_password' => ['ar' => 'تأكيد كلمة المرور', 'en' => 'Confirm Password'],
            'role_select' => ['ar' => 'أريد أن:', 'en' => 'I want to:'],
            'role_student_title' => ['ar' => 'أبحث عن خدمة', 'en' => 'I need a service'],
            'role_student_desc' => ['ar' => 'أنا طالب وأحتاج مساعدة في إنجاز عمل (تصميم، كتابة، برمجة...) وسأقوم بنشر طلب.', 'en' => 'I am a student looking for help with a task and I will post a request.'],
            'role_client_title' => ['ar' => 'أقدم خدمة', 'en' => 'I offer a service'],
            'role_client_desc' => ['ar' => 'أمتلك مهارة وأريد العمل كمستقل وتقديم عروض على الطلبات لأربح مالاً.', 'en' => 'I have a skill and want to work as a freelancer, bid on requests and earn money.'],
            'register_btn' => ['ar' => 'إنشاء الحساب', 'en' => 'Register'],
            'already_have_account' => ['ar' => 'لديك حساب بالفعل؟', 'en' => 'Already have an account?'],
            'error_fields_required' => ['ar' => 'جميع الحقول مطلوبة', 'en' => 'All fields are required'],
            'error_password_mismatch' => ['ar' => 'كلمة المرور غير متطابقة', 'en' => 'Passwords do not match'],
            'error_password_length' => ['ar' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل', 'en' => 'Password must be at least 8 characters'],
            'error_username_exists' => ['ar' => 'اسم المستخدم أو البريد الإلكتروني موجود مسبقاً', 'en' => 'Username or email already exists'],
            'error_invalid_credentials' => ['ar' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة', 'en' => 'Invalid email or password'],

            // Static pages
            'about_title' => ['ar' => 'عن StudentLance', 'en' => 'About StudentLance'],
            'about_text' => ['ar' => 'منصة متخصصة لربط الطلاب المحتاجين لخدمات مع المستقلين.', 'en' => 'A platform connecting students in need of services with freelancers.'],
            'contact_title' => ['ar' => 'اتصل بنا', 'en' => 'Contact Us'],
            'contact_text' => ['ar' => 'للاستفسارات: info@studentlance.com', 'en' => 'For inquiries: info@studentlance.com'],
            'privacy_title' => ['ar' => 'سياسة الخصوصية', 'en' => 'Privacy Policy'],
            'privacy_last_update' => ['ar' => 'آخر تحديث: 1 يناير 2024', 'en' => 'Last updated: January 1, 2024'],
            'privacy_section1_title' => ['ar' => '1. المعلومات التي نجمعها', 'en' => '1. Information We Collect'],
            'privacy_section1_text' => ['ar' => 'نجمع الاسم، البريد، المهارات، والمشاريع.', 'en' => 'We collect name, email, skills, and projects.'],
            'privacy_section2_title' => ['ar' => '2. كيفية استخدام المعلومات', 'en' => '2. How We Use Information'],
            'privacy_section2_text' => ['ar' => 'لتشغيل المنصة وتحسينها.', 'en' => 'To operate and improve the platform.'],
            'privacy_section3_title' => ['ar' => '3. مشاركة المعلومات', 'en' => '3. Information Sharing'],
            'privacy_section3_text' => ['ar' => 'نشارك المعلومات بين الطرفين حسب الحاجة فقط.', 'en' => 'We share information between parties as needed.'],
            'privacy_section4_title' => ['ar' => '4. أمان البيانات', 'en' => '4. Data Security'],
            'privacy_section4_text' => ['ar' => 'نطبق إجراءات أمنية لحماية بياناتك.', 'en' => 'We implement security measures.'],
            'privacy_section5_title' => ['ar' => '5. ملفات تعريف الارتباط', 'en' => '5. Cookies'],
            'privacy_section5_text' => ['ar' => 'لحفظ الجلسة واللغة.', 'en' => 'For session and language.'],
            'privacy_section6_title' => ['ar' => '6. حقوق المستخدم', 'en' => '6. User Rights'],
            'privacy_section6_text' => ['ar' => 'اطلاع، تصحيح، حذف.', 'en' => 'Access, correct, delete.'],
            'privacy_section7_title' => ['ar' => '7. التعديلات', 'en' => '7. Changes'],
            'privacy_section7_text' => ['ar' => 'نحتفظ بحق التعديل مع إشعارك.', 'en' => 'We may modify and will notify.'],
            'privacy_section8_title' => ['ar' => '8. اتصل بنا', 'en' => '8. Contact Us'],
            'privacy_section8_text' => ['ar' => 'privacy@studentlance.com', 'en' => 'privacy@studentlance.com'],

            // Footer
            'footer_about' => ['ar' => 'StudentLance', 'en' => 'StudentLance'],
            'footer_students' => ['ar' => 'للطلاب', 'en' => 'For Students'],
            'footer_clients' => ['ar' => 'للمستقلين', 'en' => 'For Freelancers'],
            'footer_connect' => ['ar' => 'تواصل', 'en' => 'Connect'],
            'footer_rights' => ['ar' => '© 2024 StudentLance. جميع الحقوق محفوظة.', 'en' => '© 2024 StudentLance. All rights reserved.'],
        ];
    }

    $lang = getLanguage();
    return $translations[$key][$lang] ?? ($translations[$key]['ar'] ?? $key);
}
?>