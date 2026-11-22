<?php
declare(strict_types=1);

function cv_template_html(string $qrUrl): string {
  $origin = parse_url($qrUrl);
  $siteHost = is_array($origin) && isset($origin['host'])
    ? $origin['host'] . (isset($origin['port']) ? ':' . $origin['port'] : '')
    : $qrUrl;
  $html = <<<'CVHTML'
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mohammad Yaghi</title>
  <style>
    * {
      box-sizing: border-box;
    }

    :root {
      --ink: #1c292b;
      --muted: #56676a;
      --line: #cbd5d1;
      --soft: #f1f5f2;
      --paper: #fffdf9;
      --blue: #243638;
      --blue-2: #3d5555;
      --green: #397a20;
      --orange: #b85c1f;
      --orange-soft: #f8eee6;
      --scroll-track: rgba(36, 54, 56, 0.08);
      --scroll-thumb-border: rgba(255, 253, 249, 0.92);
    }

    *::-webkit-scrollbar {
      width: 12px;
      height: 12px;
    }

    *::-webkit-scrollbar-track {
      background: var(--scroll-track);
      border-radius: 999px;
    }

    *::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg, #397a20 0%, #b85c1f 100%);
      border: 3px solid var(--scroll-thumb-border);
      border-radius: 999px;
      box-shadow: inset 0 0 0 1px rgba(36, 54, 56, 0.08);
    }

    *::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, #4c8a31 0%, #a94d18 100%);
    }

    *::-webkit-scrollbar-corner {
      background: transparent;
    }

    body {
      margin: 0;
      padding: 0;
      background: #edf1ef;
      color: var(--ink);
      font-family: "Aptos", "Segoe UI", Arial, sans-serif;
      line-height: 1.32;
      -webkit-font-smoothing: antialiased;
    }

    .page {
      width: 210mm;
      height: 297mm;
      margin: 14px auto;
      background: var(--paper);
      box-shadow: 0 6px 26px rgba(15, 23, 42, 0.12);
      position: relative;
      overflow: hidden;
    }

    .page::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 5.5mm;
      height: 100%;
      background: var(--blue);
    }

    .page::after {
      content: "";
      position: absolute;
      top: 0;
      left: 5.5mm;
      width: 1.1mm;
      height: 44mm;
      background: var(--orange);
    }

    .inner {
      height: 100%;
      padding: 6.5mm 10.5mm 9mm 14.5mm;
      position: relative;
      z-index: 1;
    }

    .page:nth-of-type(2) .inner {
      padding-bottom: 9mm;
    }

    .header {
      padding: 0 0 8px;
      margin-bottom: 8px;
      border-bottom: 1px solid var(--line);
      overflow: hidden;
    }

    .name-mark {
      display: block;
      overflow: hidden;
    }

    .identity-main {
      padding-top: 1px;
    }

    .photo-placeholder {
      float: left;
      width: 116px;
      height: 132px;
      margin-right: 14px;
      border: 1.3px solid var(--green);
      border-bottom: 4px solid var(--orange);
      background: #ffffff;
      border-radius: 2px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: var(--muted);
      font-size: 9px;
      font-weight: 700;
      line-height: 1.2;
      letter-spacing: 0.4px;
      overflow: hidden;
    }

    .photo-placeholder img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center 25%;
    }

    .name {
      font-size: 25px;
      line-height: 0.98;
      font-weight: 800;
      letter-spacing: -0.35px;
      color: var(--blue);
      margin: 0;
      text-transform: none;
    }

    .title {
      font-size: 12px;
      font-weight: 650;
      color: var(--orange);
      margin: 6px 0 0;
      letter-spacing: 0;
    }

    .identity-details {
      margin-top: 7px;
    }

    .detail-line {
      border-left: 3px solid var(--orange);
      padding: 3px 0 3px 7px;
      font-size: 9.7px;
      color: var(--muted);
      line-height: 1.25;
    }

    .detail-line strong {
      color: var(--blue);
      font-weight: 750;
    }

    .detail-line a {
      color: inherit;
      text-decoration: none;
    }

    .sce-strip {
      clear: both;
      margin-left: 130px;
      margin-top: 2px;
      padding: 6px 0 0;
      border-top: 1px solid var(--line);
      background: transparent;
      font-size: 9.7px;
      color: var(--blue);
    }

    .sep {
      font-size: 1.35em;
      line-height: 0;
      color: var(--orange);
      font-weight: 800;
      padding: 0 3px;
      vertical-align: -0.03em;
    }


    

    .section {
      margin-bottom: 10.2px;
    }

    .section-title {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 11.2px;
      font-weight: 800;
      color: var(--blue);
      text-transform: uppercase;
      letter-spacing: 1.15px;
      margin: 0 0 8px;
    }

    .section-title::before {
      content: "";
      width: 21px;
      height: 2px;
      background: var(--green);
      display: inline-block;
      flex: 0 0 auto;
    }

    .section-title::after {
      content: "";
      height: 1px;
      background: var(--line);
      flex: 1;
    }

    p {
      margin: 0 0 5px;
      font-size: 10.9px;
    }

    ul {
      margin: 0;
      padding-left: 15px;
    }

    li {
      margin-bottom: 3px;
      font-size: 10.5px;
    }

    .summary-box {
      background: transparent;
      border: 0;
      border-left: 3px solid var(--green);
      padding: 2.5px 0 2.5px 9px;
    }

    .summary-box p:last-child {
      margin-bottom: 0;
    }

    .focus-line {
      color: var(--blue);
      font-weight: 700;
    }

    .tag-list {
      margin-top: 1px;
      overflow: hidden;
    }

    .tag {
      display: flex;
      align-items: center;
      float: left;
      width: 32%;
      min-height: 22px;
      border-left: 2px solid var(--green);
      background: #f4f7f5;
      padding: 3.5px 6px;
      margin: 0 2% 5px 0;
      font-size: 9.95px;
      color: var(--blue);
      font-weight: 650;
      line-height: 1.15;
    }

    .tag:nth-child(3n) {
      margin-right: 0;
    }

    .tag::after {
      content: "";
    }

    .job-card {
      border: 0;
      background: transparent;
    }

    .job-top {
      background: transparent;
      padding: 0 0 5px;
      border-bottom: 1px solid var(--line);
      overflow: hidden;
    }

    .job-top > :first-child {
      float: left;
      width: 72%;
    }

    .job-top > :last-child {
      float: right;
      width: 24%;
    }

    .job-title,
    .project-title,
    .edu-title,
    .cert-title {
      font-weight: 800;
      font-size: 11px;
      color: var(--ink);
    }

    .company,
    .edu-place,
    .cert-place {
      font-size: 10px;
      color: var(--muted);
      margin-top: 2px;
    }

    .date {
      font-size: 9.7px;
      color: var(--muted);
      white-space: nowrap;
      text-align: right;
    }

    .job-body {
      padding: 6.5px 0 0;
      column-count: 2;
      column-gap: 16px;
    }

    .job-body li {
      break-inside: avoid;
      page-break-inside: avoid;
    }

    .job-card + .job-card {
      margin-top: 8px;
    }

    .role-line {
      display: flex;
      justify-content: space-between;
      margin-top: 6px;
      font-size: 10.5px;
      color: var(--ink);
    }

    .role-line span {
      font-size: 9.7px;
      color: var(--muted);
    }

    .project-grid {
      overflow: hidden;
    }

    .project-card {
      float: left;
      width: calc(50% - 4.5px);
      border: 0;
      border-left: 2px solid var(--line);
      padding: 0 0 0 8px;
      background: transparent;
      min-height: auto;
      margin: 0 9px 5px 0;
    }

    .project-card:nth-child(2n) {
      margin-right: 0;
    }

    .project-card.highlight {
      border-left-color: var(--green);
      background: transparent;
    }

    .project-meta {
      font-size: 9.2px;
      color: var(--orange);
      font-weight: 750;
      margin: 1px 0 3px;
      text-transform: uppercase;
      letter-spacing: 0.25px;
    }

    .compact-list {
      padding-left: 14px;
    }

    .compact-list li {
      font-size: 9.85px;
      margin-bottom: 2.3px;
    }

    .two-col {
      overflow: hidden;
    }

    .two-col > * {
      float: left;
      width: calc(50% - 7px);
      margin-right: 14px;
    }

    .two-col > *:last-child {
      margin-right: 0;
    }

    .row-block {
      border-bottom: 1px solid var(--line);
      padding: 3.2px 0;
    }

    .row-block:first-of-type {
      padding-top: 0;
    }

    .row-block:last-child {
      border-bottom: 0;
    }

    .edu-row,
    .cert-row {
      overflow: hidden;
    }

    .edu-row > :first-child,
    .cert-row > :first-child {
      float: left;
      width: 72%;
    }

    .edu-row > :last-child,
    .cert-row > :last-child {
      float: right;
      width: 24%;
    }

    .skills-grid {
      overflow: hidden;
    }

    .skill-box {
      float: left;
      width: 32%;
      border: 0;
      border-top: 1px solid var(--line);
      background: transparent;
      padding: 4px 0 0;
      font-size: 9.65px;
      min-height: auto;
      margin: 0 2% 6px 0;
    }

    .skill-box:nth-child(3n) {
      margin-right: 0;
    }

    .skill-box strong {
      display: block;
      color: var(--blue);
      margin-bottom: 2px;
      font-size: 10.25px;
    }

    .language-info {
      overflow: hidden;
    }

    .language-info > * {
      float: left;
      width: 32%;
      margin-right: 2%;
    }

    .language-info > *:nth-child(3n) {
      margin-right: 0;
    }

    @supports (display: grid) {
      .header {
        display: grid;
        grid-template-columns: 124px 1fr;
        grid-template-areas:
          "photo identity"
          "photo credential";
        gap: 6px 14px;
        align-items: start;
      }

      .name-mark {
        grid-area: identity;
      }

      .photo-placeholder {
        grid-area: photo;
        float: none;
        margin-right: 0;
      }

      .identity-details {
        display: grid;
        grid-template-columns: 1fr;
        gap: 4px;
      }

      .sce-strip {
        grid-area: credential;
        clear: none;
        margin-left: 0;
      }

      .tag-list {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 5px 7px;
      }

      .tag,
      .project-card,
      .skill-box,
      .language-info > *,
      .two-col > *,
      .job-top > :first-child,
      .job-top > :last-child,
      .edu-row > :first-child,
      .edu-row > :last-child,
      .cert-row > :first-child,
      .cert-row > :last-child {
        float: none;
        width: auto;
        margin-right: 0;
      }

      .tag,
      .skill-box {
        margin-bottom: 0;
      }

      .job-top {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        align-items: start;
      }

      .project-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5px 9px;
      }

      .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
      }

      .edu-row,
      .cert-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        align-items: start;
      }

      .skills-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px 9px;
      }

      .language-info {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
      }
    }

    @supports (object-fit: cover) {
      .photo-placeholder img {
        width: 100%;
        height: 100%;
        min-width: 0;
        min-height: 0;
        object-fit: cover;
      }
    }

    .info-item {
      border: 0;
      border-top: 1px solid var(--line);
      padding: 4px 0 0;
      background: transparent;
      font-size: 9.8px;
    }

    .info-item strong {
      color: var(--blue);
    }

    .references-note {
      position: absolute;
      left: 14.5mm;
      right: 10.5mm;
      bottom: 13mm;
      padding-top: 5px;
      border-top: 1px solid var(--line);
      color: var(--muted);
      font-size: 9.4px;
      text-align: center;
      letter-spacing: 0.2px;
    }

    .qr-card {
      position: absolute;
      right: 10.5mm;
      bottom: 22mm;
      width: 82px;
      text-align: center;
    }

    .qr-card img {
      width: 82px;
      height: 82px;
      margin: 0 auto 4px;
      border: 1px solid var(--line);
      background: #ffffff;
      padding: 4px;
    }

    .page-break {
      page-break-before: auto;
    }


    @page {
      size: A4;
      margin: 0;
    }

    @media print {
      * {
        margin: 0;
        padding: 0;
      }

      html, body {
        background: #ffffff;
        margin: 0;
        padding: 0;
        width: 100%;
        height: auto;
        overflow: visible;
      }

      body {
        display: block;
      }

      .page {
        width: 210mm;
        height: 297mm;
        margin: 0;
        padding: 0;
        box-shadow: none;
        page-break-after: always;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
      }

      .page:last-of-type {
        page-break-after: auto;
      }

      .page-break {
        page-break-before: auto;
      }

      script, style[type="text/css"] {
        display: none !important;
        margin: 0 !important;
        padding: 0 !important;
        height: 0 !important;
        width: 0 !important;
        overflow: hidden !important;
      }

      .inner {
        height: 100%;
        padding: 6mm 10mm 8mm 14.5mm;
      }

      .page:nth-of-type(2) .inner {
        padding-bottom: 8mm;
      }
    }
  </style>
</head>
<body>
  <main class="page">
    <div class="inner">
      <header class="header">
        <div class="photo-placeholder">
            <img src="assets/images/CV.jpeg" alt="Mohammad Yaghi" loading="eager" fetchpriority="high" draggable="false" />
        </div>

        <div class="name-mark">
          <div class="identity-main">
            <h1 class="name">Mohammad Yaghi</h1>
            <div class="title">IT Manager · Computer Network Specialist · Systems and Technical Support</div>
            <div class="identity-details">
              <div class="detail-line"><strong>Languages:</strong> Arabic Native <span class="sep">·</span> English Professional Working Proficiency <span class="sep">·</span> <strong>Nationality:</strong> Jordanian</div>
              <div class="detail-line"><strong>Contact:</strong> Jeddah, Saudi Arabia <span class="sep">·</span> +966545748371 <span class="sep">·</span> moe@yaghi.me <span class="sep">·</span> <a href="{{CV_QR_URL}}">{{CV_SITE_HOST}}</a></div>
            </div>
          </div>
        </div>

        <div class="sce-strip">
          <strong>Saudi Council of Engineers:</strong> Specialist – Computer Network
        </div>
      </header>

      <section class="section">
        <h2 class="section-title">Professional Summary</h2>
        <div class="summary-box">
          <p>
            Hands-on IT Manager and Computer Network Specialist with Saudi-based experience supporting approximately 150 employees and around 300 cloud/email accounts across multi-company operations, including technical support, network administration, systems management, ERP administration, web and mobile applications, CCTV/security infrastructure, cloud platforms, and day-to-day IT operations.
          </p>
          <p>
            Experienced in supporting business-critical environments across commercial, environmental consulting, e-commerce, and service operations in KSA, including user and device support, network reliability, Active Directory, cloud administration, vendor coordination, technical documentation, tenders, ZATCA-related invoicing, and secure IT operations across Teeb Al Hayah Commercial Company (THCC) and its related group companies.
          </p>
        </div>
      </section>

      <section class="section">
        <h2 class="section-title">Core Competencies</h2>
        <div class="tag-list">
          <span class="tag">Network Administration</span>
          <span class="tag">Windows Server and Active Directory</span>
          <span class="tag">Microsoft 365 and Google Workspace</span>
          <span class="tag">Hosting, DNS and Domains</span>
          <span class="tag">ERP System Administration</span>
          <span class="tag">Web and Mobile Development</span>
          <span class="tag">CCTV and Access Control</span>
          <span class="tag">Cybersecurity Awareness</span>
          <span class="tag">IT Documentation and Scopes</span>
          <span class="tag">Vendor and Supplier Coordination</span>
        </div>
      </section>

      <section class="section">
        <h2 class="section-title">Work Experience</h2>
        <div class="job-card">
          <div class="job-top">
            <div>
              <div class="job-title">Teeb Al Hayah Commercial Company (THCC)</div>
              <div class="company">Jeddah, Saudi Arabia</div>
            </div>
            <div class="date">February 2022 &ndash; Present</div>
          </div>
          <div class="role-line"><strong>IT Manager</strong><span>July 2023 &ndash; Present</span></div>
          <div class="job-body">
            <ul>
              <li>Manage day-to-day IT operations, technical support, networks, systems, cloud platforms, and digital services across Teeb Al Hayah Commercial Company (THCC) and its related group companies in Saudi Arabia, supporting around 150 employees across 4 offices, 2 warehouses, and additional private business environments.</li>
              <li>Provided responsive IT support across multiple companies, handling a high volume of employee requests and resolving issues promptly.</li>
              <li>Administer network and system infrastructure, including routers, switches, access points, firewalls, Windows Server, Active Directory, NAS/NVR/server hardware, shared folders, permissions, and backups, and provide end-user support for computers, mobile devices, printers, email, and internal systems.</li>
              <li>Manage 7 domains/websites and around 300 Microsoft 365 and Google Workspace accounts, including hosting, DNS, SSL, email, databases, deployments, migrations, and APIs across cPanel, Bluehost, Hostinger, iFastNet, and AWS EC2; build and maintain websites and platforms for THCC, Teeb Al Hayah Environmental Consulting, Makine, Man of Quality (QMANCO), and Crearoma, including its Zid e-commerce store.</li>
              <li>Administered and supported Odoo.sh Enterprise ERP under the THCC umbrella across 6 companies and multiple departments, managing users, permissions, workflows, forms, fields, reports, testing, and implementation coordination.</li>
              <li>Developed and managed internal PHP/MySQL web tools that reduced manual work across sales, contracts, field operations, attendance, reporting, and support requests, including a Linux-based (Ubuntu/Manjaro) attendance system built in 2024 for THCC and its related group companies.</li>
              <li>Supported low-current and security systems including CCTV, NVR/storage, access control, sensors, cabling, monitoring rooms, remote access, and Grandstream PBX/IP telephony, covering configuration, testing, and supplier coordination; contributed to the Zamzam Well security concept and to establishing a direct business relationship with Hikvision KSA.</li>
              <li>Coordinated vendors and suppliers for IT, networking, and CCTV/security equipment, and prepared technical scopes, requirements, diagrams, and documentation for IT, ERP, security, and low-current projects, supporting tenders, CR/VAT/ZATCA requirements, ZATCA invoicing, and telecom contracts.</li>
              <li>Supported cybersecurity improvement efforts, including user awareness, access control, device management, policy documentation, and Saudi Aramco CCC-related compliance support for the environmental consulting company.</li>
            </ul>
          </div>
          <div class="role-line"><strong>Data Entry with IT Responsibilities</strong><span>February 2022 &ndash; July 2023</span></div>
          <div class="job-body">
            <ul>
              <li>Hired as a data entry employee and took on IT responsibilities as the business needed them.</li>
              <li>Kept learning and applying new skills by building increasingly capable internal tools, which led to the official IT Manager title in July 2023.</li>
            </ul>
          </div>
        </div>
        <div class="job-card">
          <div class="job-top">
            <div>
              <div class="job-title">Independent Learning &amp; Job Search &ndash; Jordan</div>
              <div class="company">Self-taught HTML, CSS, PHP, and MySQL while applying for IT roles; completed the CCNA course (Jul 2019) and Advanced PHP Web Development Course (Nov 2021).</div>
            </div>
            <div class="date">2019 &ndash; 2022</div>
          </div>
        </div>
      </section>

    </div>
  </main>

  <main class="page page-break">
    <div class="inner">
      <section class="section">
        <h2 class="section-title">Selected Projects</h2>
        <div class="project-grid">
          <div class="project-card">
            <div class="project-title">ERP Consolidation Project</div>
            <div class="project-meta">Group-Level Internal Project</div>
            <ul class="compact-list">
              <li>Served as ERP administrator for Odoo.sh Enterprise under THCC umbrella operations, supporting users, workflows, custom forms, fields, permissions, reporting, and operational needs.</li>
              <li>Covered 6 companies and major departments including accounting, sales, HR, operations, inventory, management, payroll, projects, websites, manufacturing, and construction project workflows.</li>
              <li>Reduced duplicated reporting and manual forms by making operational information available directly through the ERP for management and departments.</li>
            </ul>
          </div>
          <div class="project-card highlight">
            <div class="project-title">Haram Smart Charging and Safe-Keeping System</div>
            <div class="project-meta">2025 Tender · Man of Quality (QMANCO)</div>
            <ul class="compact-list">
              <li>Originated the solution concept for the client and led its delivery end to end, including sourcing, supplier communication, Sunmi POS supply, and workflow coordination.</li>
              <li>Supported QR/barcode receipt printing, device handover and return tracking, stakeholder approval, tender success, and Ramadan operation across 16 areas.</li>
            </ul>
          </div>
          <div class="project-card highlight">
            <div class="project-title">Holy Sites · Al-Mashaer Mosque Security Systems</div>
            <div class="project-meta">2025 Security Tender · Man of Quality (QMANCO)</div>
            <ul class="compact-list">
              <li>Worked on CCTV, cabling, programming, monitoring/control room connectivity, and configuration for 4 mosque locations in the Holy Sites / Al-Mashaer area.</li>
              <li>Supported design, supply, installation, configuration, testing, and operational readiness while maintaining separation from existing public security camera systems.</li>
            </ul>
          </div>
          <div class="project-card">
            <div class="project-title">Internal Business Systems</div>
            <div class="project-meta">Operations Tools</div>
            <ul class="compact-list">
              <li>Developed 2023 Crearoma tools for sales tracking, client contracts, oil refill tracking, operations, and internal follow-up.</li>
              <li>Built practical PHP/MySQL systems to reduce manual work, improve record visibility, and support operational reporting.</li>
              <li>Built tools for Teeb Al Hayah Environmental Consulting, including a ZATCA invoicing system delivered within one week, and a field survey image processor for watermarking, written details, and location information on images.</li>
            </ul>
          </div>
        </div>
      </section>
      <section class="section">
        <h2 class="section-title">Education</h2>
        <div class="row-block edu-row">
          <div>
            <div class="edu-title">Bachelor’s Degree in Computer Networks</div>
            <div class="edu-place">Jerash University – Jordan</div>
          </div>
          <div class="date">2019</div>
        </div>
      </section>

      <section class="section">
        <h2 class="section-title">Certifications & Professional Development</h2>
        <div class="row-block cert-row">
          <div>
            <div class="cert-title">Cybersecurity Training Diploma</div>
            <div class="cert-place">300 hours &middot; Two certificates: AUM Consultations, Studies and Training Center with Pioneers Academy, and RES (programme reviewed by the University of Buckingham, 300 CPD Hours)</div>
          </div>
          <div class="date">Oct 2024 &ndash; Oct 2025</div>
        </div>
        <div class="row-block cert-row">
          <div>
            <div class="cert-title">Introduction to Network Engineering</div>
            <div class="cert-place">Pioneers Academy <span class="sep">·</span> 16 Hours</div>
          </div>
          <div class="date">Jan 2025</div>
        </div>
        <div class="row-block cert-row">
          <div>
            <div class="cert-title">CSCU Certified Secure Computer User</div>
            <div class="cert-place">Pioneers Academy <span class="sep">·</span> 20 Hours</div>
          </div>
          <div class="date">Dec 2024 – Jan 2025</div>
        </div>
        <div class="row-block cert-row">
          <div>
            <div class="cert-title">CompTIA A+ Course</div>
            <div class="cert-place">Pioneers Academy <span class="sep">·</span> 30 Hours</div>
          </div>
          <div class="date">Nov 2024 – Dec 2024</div>
        </div>
        <div class="row-block cert-row">
          <div>
            <div class="cert-title">Digital Literacy</div>
            <div class="cert-place">Pioneers Academy <span class="sep">·</span> 24 Hours</div>
          </div>
          <div class="date">Oct 2024 – Nov 2024</div>
        </div>
        <div class="row-block cert-row">
          <div>
            <div class="cert-title">Advanced PHP Web Development Course</div>
            <div class="cert-place">Pioneers Academy – Amman, Jordan</div>
          </div>
          <div class="date">Nov 2021</div>
        </div>
        <div class="row-block cert-row">
          <div>
            <div class="cert-title">CCNA Course</div>
            <div class="cert-place">Jerash University – Jordan</div>
          </div>
          <div class="date">Jul 2019</div>
        </div>
      </section>

      <section class="section">
        <h2 class="section-title">Technical Skills</h2>
        <div class="skills-grid">
          <div class="skill-box">
            <strong>Networking</strong>
            LAN, VLANs, VPNs, firewall rules, routers, switches, PoE switches, access points, structured cabling, troubleshooting, documentation, Cisco, Mikrotik, Ruijie, Huawei, TP-Link, D-Link, Linksys.
          </div>
          <div class="skill-box">
            <strong>Systems</strong>
            Windows devices, Windows Server, Active Directory, domain services, shared folders, permissions, Linux Ubuntu/Manjaro, NAS/server hardware, backups, MyQ print management, user/device support.
          </div>
          <div class="skill-box">
            <strong>Security & Low Current</strong>
            CCTV, NVR, IP cameras, remote access, camera backups, access control, face terminals, monitoring rooms, sensors, Hikvision, Dahua, Grandstream PBX/IP phones.
          </div>
          <div class="skill-box">
            <strong>Cloud, Hosting & Platforms</strong>
            Microsoft 365 Enterprise Admin, Exchange, Teams, Zoom, Google Workspace, cPanel, Bluehost, Hostinger, iFastNet, AWS EC2, domains, DNS, SSL, emails, backups, databases, deployments, migrations, APIs, subdomains.
          </div>
          <div class="skill-box">
            <strong>Software Development &amp; Business Systems</strong>
            Web and mobile applications, business systems, databases and integrations. Technologies include Python, JavaScript, PHP, C++, C#, Kotlin, Flutter, MySQL, HTML and CSS. Odoo ERP administration and Zid coordination.
          </div>
          <div class="skill-box">
            <strong>Documentation & Design Support</strong>
            Scope of work, technical requirements, tender support, project documentation, diagrams, user guides, policies, AutoCAD layouts.
          </div>
          <div class="skill-box">
            <strong>Communication</strong>
            Arabic and English business communication, vendor coordination, department support, team collaboration.
          </div>
        </div>
      </section>

      <div class="qr-card">
        <div class="cv-qr" data-qr-value="{{CV_QR_URL}}"></div>
      </div>

      <div class="references-note">References available upon request.</div>
    </div>
  </main>
  <script src="/assets/vendor/qrcode-generator/qrcode.js"></script>
  <script>
    (function () {
      var container = document.querySelector('.cv-qr');
      if (!container || typeof qrcode !== 'function') { return; }
      try {
        var target = container.getAttribute('data-qr-value');
        var code = qrcode(0, 'H');
        code.addData(target);
        code.make();
        var image = document.createElement('img');
        image.src = code.createDataURL(4, 4);
        image.alt = 'QR code linking to ' + target;
        container.replaceChildren(image);
      } catch (error) {}
    })();
  </script>
</body>
</html>
CVHTML;
  return strtr($html, [
    '{{CV_QR_URL}}' => htmlspecialchars($qrUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
    '{{CV_SITE_HOST}}' => htmlspecialchars($siteHost, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
  ]);
}
