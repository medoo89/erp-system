@extends('layouts.website')

@section('title', 'Sada Fezzan Oil Services Company | Oil & Gas Support Solutions in Libya')

@section('content')

@php
    $jobsUrl = url('/jobs');
@endphp

<section class="hero">
    <div class="container hero-grid">
        <div>
            <div class="eyebrow">
                <span class="eyebrow-dot"></span>
                <span data-en>Libya-Based Oil & Gas Support Solutions</span>
                <span data-ar>حلول دعم لقطاع النفط والغاز في ليبيا</span>
            </div>

            <h1>
                <span data-en>Reliable field support for the energy sector.</span>
                <span data-ar>دعم ميداني موثوق لقطاع الطاقة.</span>
            </h1>

            <p>
                <span data-en>Sada Fezzan Oil Services Company provides manpower, mobilization, logistics, maintenance, drilling support, HSE coordination, and technical field support for oil and gas operations across Libya.</span>
                <span data-ar>تقدم شركة صدى فزان للخدمات النفطية خدمات القوى العاملة، التعبئة، اللوجستيات، الصيانة، دعم الحفر، تنسيق السلامة، والدعم الفني الميداني لعمليات النفط والغاز في ليبيا.</span>
            </p>

            @if (session('success'))
                <div class="success-alert">
                    <span data-en>{{ session('success') }}</span>
                    <span data-ar>تم إرسال طلبك بنجاح. سيقوم فريقنا بالتواصل معك قريبًا.</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="error-alert">
                    <span data-en>Please check the form fields and try again.</span>
                    <span data-ar>يرجى مراجعة بيانات النموذج والمحاولة مرة أخرى.</span>
                </div>
            @endif

            <div class="hero-actions">
                <a href="{{ $jobsUrl }}" class="btn btn-primary">
                    <span class="material-symbols-rounded">business_center</span>
                    <span data-en>Open Career Portal</span>
                    <span data-ar>فتح بوابة الوظائف</span>
                </a>

                <a href="{{ $jobsUrl }}" class="btn btn-jobs">
                    <span class="material-symbols-rounded">work</span>
                    <span data-en>Job Opportunities</span>
                    <span data-ar>فرص العمل</span>
                </a>

                <a href="#services" class="btn btn-outline">
                    <span class="material-symbols-rounded">explore</span>
                    <span data-en>Explore Capabilities</span>
                    <span data-ar>استكشف الخدمات</span>
                </a>
            </div>

            <div class="jobs-hero-card">
                <div>
                    <strong>
                        <span data-en>Looking for oil & gas job opportunities?</span>
                        <span data-ar>هل تبحث عن فرص عمل في قطاع النفط والغاز؟</span>
                    </strong>
                    <span data-en>View current openings and submit your application directly through SADAFazan careers.</span>
                    <span data-ar>اطلع على الوظائف المتاحة وقدّم طلبك مباشرة من خلال بوابة التوظيف الخاصة بصدى فزان.</span>
                </div>

                <a href="{{ $jobsUrl }}" class="btn btn-jobs">
                    <span class="material-symbols-rounded">assignment_ind</span>
                    <span data-en>Apply Now</span>
                    <span data-ar>قدّم الآن</span>
                </a>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">2024</div>
                    <div class="stat-label">
                        <span data-en>Established in Libya</span>
                        <span data-ar>تأسست في ليبيا</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-value">20+</div>
                    <div class="stat-label">
                        <span data-en>Years founding team experience</span>
                        <span data-ar>سنوات خبرة لفريق التأسيس</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-value">24/7</div>
                    <div class="stat-label">
                        <span data-en>Mobilization coordination mindset</span>
                        <span data-ar>جاهزية تنسيق التعبئة والمتابعة</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="visual-card">
                <img src="https://sfco.ly/crane.png" alt="Oilfield support visual">

                <div class="floating-note">
                    <div class="floating-note-title">
                        <span class="material-symbols-rounded">manufacturing</span>
                        <span data-en>Operational Readiness</span>
                        <span data-ar>الجاهزية التشغيلية</span>
                    </div>
                    <p>
                        <span data-en>From candidate preparation and documentation to PPE, travel, mobilization, and site follow-up.</span>
                        <span data-ar>من تجهيز المرشحين والمستندات إلى معدات السلامة، السفر، التعبئة، والمتابعة الميدانية.</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="about" class="section section-soft">
    <div class="container split">
        <div>
            <div class="section-label">
                <span data-en>About Sada Fezzan</span>
                <span data-ar>عن صدى فزان</span>
            </div>
            <h2 class="section-title">
                <span data-en>Built for Libya’s oil and gas operating environment.</span>
                <span data-ar>مصممة لخدمة بيئة تشغيل النفط والغاز في ليبيا.</span>
            </h2>
        </div>

        <div class="large-text">
            <p>
                <span data-en>Established in 2024, Sada Fezzan Oil Services Company supports oil services operations in Libya with a comprehensive service environment and a founding team carrying more than 20 years of experience.</span>
                <span data-ar>تأسست شركة صدى فزان للخدمات النفطية في عام 2024 لدعم عمليات الخدمات النفطية في ليبيا من خلال بيئة خدمات متكاملة وفريق تأسيسي يمتلك أكثر من 20 سنة من الخبرة.</span>
            </p>

            <p>
                <span data-en>The upgraded digital presence positions the company as a trusted technical partner for manpower, field support, logistics, HSE readiness, maintenance, and operational coordination.</span>
                <span data-ar>يعكس هذا الموقع المطوّر هوية الشركة كشريك فني موثوق في القوى العاملة، الدعم الميداني، اللوجستيات، جاهزية السلامة، الصيانة، والتنسيق التشغيلي.</span>
            </p>
        </div>
    </div>
</section>

<section id="services" class="section">
    <div class="container">
        <div class="center-title">
            <div class="section-label">
                <span data-en>Capabilities</span>
                <span data-ar>القدرات</span>
            </div>
            <h2 class="section-title">
                <span data-en>Integrated oilfield support services</span>
                <span data-ar>خدمات دعم متكاملة للحقول النفطية</span>
            </h2>
            <p class="large-text">
                <span data-en>A premium services hub designed around the real needs of energy clients, contractors, candidates, and vendors.</span>
                <span data-ar>منصة خدمات احترافية مصممة لتلبية احتياجات عملاء الطاقة، المقاولين، المرشحين، والموردين.</span>
            </p>
        </div>

        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon"><span class="material-symbols-rounded">engineering</span></div>
                <h3><span data-en>Oil & Gas Manpower Supply</span><span data-ar>توفير الكوادر لقطاع النفط والغاز</span></h3>
                <p><span data-en>Qualified technical personnel, engineers, supervisors, technicians, and field support teams for demanding energy operations.</span><span data-ar>توفير كوادر فنية مؤهلة من مهندسين ومشرفين وفنيين وفرق دعم ميداني للعمليات التشغيلية.</span></p>
                <span class="learn-link"><span data-en>Learn more →</span><span data-ar>اعرف المزيد ←</span></span>
            </div>

            <div class="service-card">
                <div class="service-icon"><span class="material-symbols-rounded">assignment_ind</span></div>
                <h3><span data-en>Recruitment & Mobilization</span><span data-ar>التوظيف والتعبئة</span></h3>
                <p><span data-en>CV screening, documentation, interviews, medical coordination, PPE control, travel readiness, and site mobilization follow-up.</span><span data-ar>فرز السير الذاتية، المستندات، المقابلات، التنسيق الطبي، معدات السلامة، السفر، والمتابعة الميدانية.</span></p>
                <span class="learn-link"><span data-en>Learn more →</span><span data-ar>اعرف المزيد ←</span></span>
            </div>

            <div class="service-card">
                <div class="service-icon"><span class="material-symbols-rounded">construction</span></div>
                <h3><span data-en>Maintenance & Operations Support</span><span data-ar>دعم الصيانة والتشغيل</span></h3>
                <p><span data-en>Operational support for oil and gas facilities, maintenance teams, tools, equipment, and field execution requirements.</span><span data-ar>دعم تشغيلي للمرافق النفطية وفرق الصيانة والمعدات ومتطلبات التنفيذ الميداني.</span></p>
                <span class="learn-link"><span data-en>Learn more →</span><span data-ar>اعرف المزيد ←</span></span>
            </div>

            <div class="service-card">
                <div class="service-icon"><span class="material-symbols-rounded">factory</span></div>
                <h3><span data-en>Drilling & Production Support</span><span data-ar>دعم الحفر والإنتاج</span></h3>
                <p><span data-en>Support services for drilling, well maintenance, production assistance, and technical field coordination.</span><span data-ar>خدمات دعم للحفر وصيانة الآبار ومساندة الإنتاج والتنسيق الفني الميداني.</span></p>
                <span class="learn-link"><span data-en>Learn more →</span><span data-ar>اعرف المزيد ←</span></span>
            </div>

            <div class="service-card">
                <div class="service-icon"><span class="material-symbols-rounded">local_shipping</span></div>
                <h3><span data-en>Logistics & Field Coordination</span><span data-ar>اللوجستيات والتنسيق الميداني</span></h3>
                <p><span data-en>Personnel movement, transportation coordination, supply chain support, airport coordination, and field communication.</span><span data-ar>تنسيق حركة الأفراد والنقل وسلاسل الإمداد والمطار والتواصل الميداني.</span></p>
                <span class="learn-link"><span data-en>Learn more →</span><span data-ar>اعرف المزيد ←</span></span>
            </div>

            <div class="service-card">
                <div class="service-icon"><span class="material-symbols-rounded">health_and_safety</span></div>
                <h3><span data-en>HSE & PPE Coordination</span><span data-ar>السلامة ومعدات الوقاية</span></h3>
                <p><span data-en>Safety readiness, PPE issuing records, certificate coordination, medical fitness support, and client compliance documentation.</span><span data-ar>جاهزية السلامة وسجلات تسليم معدات الوقاية والشهادات واللياقة الطبية ومتطلبات العملاء.</span></p>
                <span class="learn-link"><span data-en>Learn more →</span><span data-ar>اعرف المزيد ←</span></span>
            </div>
        </div>
    </div>
</section>

<section id="hse" class="section">
    <div class="container">
        <div class="premium-panel hse-grid">
            <div>
                <div class="section-label" style="color: var(--gold);">
                    <span data-en>HSE & Compliance</span>
                    <span data-ar>السلامة والامتثال</span>
                </div>
                <h2 class="section-title">
                    <span data-en>Safety readiness before field readiness.</span>
                    <span data-ar>جاهزية السلامة قبل الجاهزية الميدانية.</span>
                </h2>
                <p class="large-text">
                    <span data-en>The website includes a dedicated HSE and compliance layer covering PPE control, medical readiness, certificates, training coordination, and client documentation requirements.</span>
                    <span data-ar>يتضمن الموقع قسمًا مخصصًا للسلامة والامتثال يشمل التحكم في معدات الوقاية، الجاهزية الطبية، الشهادات، التدريب، ومتطلبات مستندات العملاء.</span>
                </p>
            </div>

            <div class="check-grid">
                <div class="check-card"><span class="material-symbols-rounded">check_circle</span><span data-en>PPE issuing records</span><span data-ar>سجلات تسليم معدات الوقاية</span></div>
                <div class="check-card"><span class="material-symbols-rounded">check_circle</span><span data-en>Medical fitness coordination</span><span data-ar>تنسيق اللياقة الطبية</span></div>
                <div class="check-card"><span class="material-symbols-rounded">check_circle</span><span data-en>Training and certificate tracking</span><span data-ar>متابعة التدريب والشهادات</span></div>
                <div class="check-card"><span class="material-symbols-rounded">check_circle</span><span data-en>Client documentation control</span><span data-ar>إدارة مستندات العملاء</span></div>
                <div class="check-card"><span class="material-symbols-rounded">check_circle</span><span data-en>Mobilization checklists</span><span data-ar>قوائم مراجعة التعبئة</span></div>
                <div class="check-card"><span class="material-symbols-rounded">check_circle</span><span data-en>Policy downloads</span><span data-ar>تحميل السياسات</span></div>
            </div>
        </div>
    </div>
</section>

<section id="careers" class="section">
    <div class="container">
        <div class="center-title">
            <div class="section-label"><span data-en>Recruitment Workflow</span><span data-ar>مسار التوظيف</span></div>
            <h2 class="section-title"><span data-en>From application to mobilization</span><span data-ar>من التقديم إلى التعبئة</span></h2>
            <p class="large-text"><span data-en>A clear interactive process that matches SADAFazan’s manpower and mobilization work.</span><span data-ar>مسار واضح يعكس عمل صدى فزان في التوظيف والتعبئة.</span></p>
        </div>

        <div class="process-grid">
            <div class="process-step"><div class="process-number">1</div><div class="process-label"><span data-en>Client Request</span><span data-ar>طلب العميل</span></div></div>
            <div class="process-step"><div class="process-number">2</div><div class="process-label"><span data-en>Technical Screening</span><span data-ar>الفرز الفني</span></div></div>
            <div class="process-step"><div class="process-number">3</div><div class="process-label"><span data-en>Documentation</span><span data-ar>المستندات</span></div></div>
            <div class="process-step"><div class="process-number">4</div><div class="process-label"><span data-en>Medical / Training</span><span data-ar>الفحص / التدريب</span></div></div>
            <div class="process-step"><div class="process-number">5</div><div class="process-label"><span data-en>PPE & Travel</span><span data-ar>معدات الوقاية والسفر</span></div></div>
            <div class="process-step"><div class="process-number">6</div><div class="process-label"><span data-en>Mobilization</span><span data-ar>التعبئة</span></div></div>
            <div class="process-step"><div class="process-number">7</div><div class="process-label"><span data-en>Site Follow-up</span><span data-ar>المتابعة الميدانية</span></div></div>
        </div>
    </div>
</section>

<section id="vendors" class="section section-soft">
    <div class="container three-grid">
        <div class="feature-card">
            <div class="feature-icon"><span class="material-symbols-rounded">business_center</span></div>
            <h3><span data-en>Client Portal Direction</span><span data-ar>بوابة العملاء</span></h3>
            <p><span data-en>Business inquiries, RFQs, manpower requests, and company profile requests.</span><span data-ar>استفسارات الأعمال وطلبات عروض الأسعار وطلبات القوى العاملة وطلب ملف الشركة.</span></p>
        </div>

        <div class="feature-card">
            <div class="feature-icon"><span class="material-symbols-rounded">fact_check</span></div>
            <h3><span data-en>Vendor Registration</span><span data-ar>تسجيل الموردين</span></h3>
            <p><span data-en>Supplier details, company profile upload, registration documents, and service categories.</span><span data-ar>بيانات الموردين ورفع ملف الشركة ومستندات التسجيل وتصنيف الخدمات.</span></p>
        </div>

        <div class="feature-card">
            <div class="feature-icon"><span class="material-symbols-rounded">download</span></div>
            <h3><span data-en>Downloads Library</span><span data-ar>مكتبة التحميلات</span></h3>
            <p><span data-en>Company profile, policies, forms, certificates, and controlled document access.</span><span data-ar>ملف الشركة والسياسات والنماذج والشهادات والوصول المنظم للمستندات.</span></p>
        </div>
    </div>
</section>

<section id="inquiry" class="section">
    <div class="container form-grid">
        <div>
            <div class="section-label"><span data-en>Start Here</span><span data-ar>ابدأ من هنا</span></div>
            <h2 class="section-title"><span data-en>Smart inquiry form for clients and vendors.</span><span data-ar>نموذج استفسار للعملاء والموردين.</span></h2>
            <p class="large-text">
                <span data-en>This form sends the request directly to info@sfco.ly. Job applications are handled through the dedicated career portal.</span>
                <span data-ar>يرسل هذا النموذج الطلب مباشرة إلى info@sfco.ly. أما طلبات التوظيف فتتم من خلال بوابة الوظائف المخصصة.</span>
            </p>
        </div>

        <form class="form-card" method="POST" action="{{ route('website.inquiry.submit') }}">
            @csrf

            <div class="fields">
                <label class="field-full">
                    <span data-en>Inquiry Type</span>
                    <span data-ar>نوع الاستفسار</span>
                    <select name="inquiry_type" required>
                        <option value="Request Manpower">Request Manpower</option>
                        <option value="Submit RFQ">Submit RFQ</option>
                        <option value="Register as Vendor">Register as Vendor</option>
                        <option value="Request Company Profile">Request Company Profile</option>
                        <option value="General Inquiry">General Inquiry</option>
                    </select>
                </label>

                <label>
                    <span data-en>Full Name</span>
                    <span data-ar>الاسم الكامل</span>
                    <input type="text" name="full_name" placeholder="Your full name" value="{{ old('full_name') }}" required>
                </label>

                <label>
                    <span data-en>Company / Organization</span>
                    <span data-ar>الشركة / الجهة</span>
                    <input type="text" name="company" placeholder="Company name" value="{{ old('company') }}">
                </label>

                <label>
                    <span data-en>Email Address</span>
                    <span data-ar>البريد الإلكتروني</span>
                    <input type="email" name="email" placeholder="name@company.com" value="{{ old('email') }}" required>
                </label>

                <label>
                    <span data-en>Phone / WhatsApp</span>
                    <span data-ar>الهاتف / واتساب</span>
                    <input type="text" name="phone" placeholder="+218..." value="{{ old('phone') }}">
                </label>

                <label class="field-full">
                    <span data-en>Message</span>
                    <span data-ar>الرسالة</span>
                    <textarea name="message" placeholder="Tell us about your request" required>{{ old('message') }}</textarea>
                </label>

                <div class="field-full">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <span class="material-symbols-rounded">send</span>
                        <span data-en>Submit Request</span>
                        <span data-ar>إرسال الطلب</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection
