<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Peer Evaluation')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
    <style>
      :root {
        --maroon: #7a1022;
        --maroon-dark: #540916;
        --maroon-soft: #f8e8eb;
        --maroon-border: #e8c4cb;
        --ink: #221f22;
        --muted: #6f6670;
        --surface: #ffffff;
        --page: #faf7f8;
        --line: #eadde0;
        --nav-height: 57px;
        --sidebar-width: 270px;
      }

      body {
        background:
          linear-gradient(180deg, rgba(122, 16, 34, .05), rgba(122, 16, 34, 0) 280px),
          var(--page);
        color: var(--ink);
      }

      .navbar {
        position: sticky;
        top: 0;
        z-index: 1030;
        min-height: var(--nav-height);
        box-shadow: 0 1px 0 rgba(122, 16, 34, .08);
      }

      .navbar-brand { color: var(--maroon) !important; }
      .nav-link { color: #5f5358; }
      .nav-link:hover, .nav-link:focus, .navbar .nav-link.active { color: var(--maroon); }

      .top-profile-button {
        display: inline-flex;
        align-items: center;
        gap: .65rem;
        padding: .35rem .45rem .35rem .35rem;
        border: 1px solid var(--line);
        border-radius: 999px;
        background: #fff;
        color: var(--ink);
        text-decoration: none;
      }

      .top-profile-button:hover,
      .top-profile-button:focus {
        border-color: var(--maroon-border);
        background: var(--maroon-soft);
        color: var(--maroon);
      }

      .top-profile-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.15;
      }

      .top-profile-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        flex: 0 0 2rem;
        border-radius: 999px;
        background: var(--maroon);
        color: #fff;
        font-weight: 800;
      }

      .admin-profile-hero {
        padding: 1rem;
        border: 1px solid var(--maroon-border);
        border-radius: 8px;
        background:
          linear-gradient(135deg, rgba(255, 255, 255, .72), rgba(255, 255, 255, 0)),
          var(--maroon-soft);
      }

      .admin-profile-card {
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 1rem;
        background: #fff;
        height: 100%;
      }

      .admin-profile-avatar-lg {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3.25rem;
        height: 3.25rem;
        flex: 0 0 3.25rem;
        border-radius: 999px;
        background: var(--maroon);
        color: #fff;
        font-size: 1.35rem;
        font-weight: 800;
      }

      .icon {
        width: 1rem;
        height: 1rem;
        stroke-width: 2.2;
        vertical-align: -0.15em;
      }

      .navbar-brand .icon {
        color: var(--maroon);
        width: 1.15rem;
        height: 1.15rem;
      }

      .nav-link .icon,
      .btn .icon {
        margin-right: .35rem;
      }

      .icon-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 8px;
        background: var(--maroon-soft);
        color: var(--maroon);
      }

      .icon-pill .icon {
        width: 1.15rem;
        height: 1.15rem;
      }

      .app-shell { min-height: calc(100vh - var(--nav-height)); }
      .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1040;
        align-self: flex-start;
        width: var(--sidebar-width);
        min-height: 100vh;
        overflow-y: auto;
        background: linear-gradient(180deg, var(--maroon-dark), var(--maroon));
        border-right: 1px solid rgba(255, 255, 255, .14);
        box-shadow: 12px 0 30px rgba(84, 9, 22, .10);
      }

      .sidebar .nav-link {
        color: rgba(255, 255, 255, .82);
        border-radius: 8px;
        padding: .65rem .8rem;
        font-weight: 500;
      }

      .sidebar .nav-link:hover,
      .sidebar .nav-link.active {
        background: rgba(255, 255, 255, .16);
        color: #fff;
      }

      .admin-content {
        min-width: 0;
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        flex: 0 0 calc(100% - var(--sidebar-width));
      }
      .page-title { letter-spacing: 0; }
      .text-secondary { color: var(--muted) !important; }

      .card {
        border-radius: 8px;
        border: 1px solid var(--line) !important;
        box-shadow: 0 12px 30px rgba(84, 9, 22, .07) !important;
      }

      .card-header {
        border-bottom-color: var(--line);
        border-radius: 8px 8px 0 0 !important;
      }

      .metric { border-left: 4px solid var(--maroon); }
      .progress { background-color: var(--maroon-soft); height: .65rem; }
      .progress-bar { background-color: var(--maroon) !important; }

      .block-card {
        overflow: hidden;
      }

      .block-card::before {
        content: "";
        display: block;
        height: 4px;
        background: var(--maroon);
      }

      .block-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .65rem;
      }

      .block-stat {
        padding: .75rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fffdfd;
      }

      .block-stat-value {
        font-size: 1.2rem;
        line-height: 1;
        font-weight: 800;
        color: var(--maroon);
      }

      .block-progress-label {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: .45rem;
        font-size: .85rem;
        color: var(--muted);
      }

      .group-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .65rem;
        padding: 1rem;
        border-bottom: 1px solid var(--line);
        background: #fffdfd;
      }

      .group-summary-item {
        min-width: 0;
      }

      .group-summary-value {
        font-weight: 800;
        color: var(--maroon);
        line-height: 1;
      }

      .member-row {
        cursor: pointer;
      }

      .member-row:focus-visible {
        outline: 3px solid rgba(122, 16, 34, .22);
        outline-offset: -3px;
      }

      .member-row .row-action-icon {
        color: var(--muted);
        opacity: .72;
      }

      .member-row:hover .row-action-icon,
      .member-row:focus .row-action-icon {
        color: var(--maroon);
        opacity: 1;
      }

      .evaluation-member-list {
        display: grid;
        gap: .65rem;
      }

      .evaluation-member-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .85rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
      }

      .mean-panel {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid var(--maroon-border);
        border-radius: 8px;
        background: var(--maroon-soft);
      }

      .completion-chart {
        display: grid;
        gap: .85rem;
      }

      .completion-chart-row {
        display: grid;
        grid-template-columns: minmax(150px, 220px) minmax(220px, 1fr) auto;
        gap: 1rem;
        align-items: center;
        padding: .85rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fffdfd;
      }

      .completion-chart-label {
        min-width: 0;
      }

      .completion-chart-label .fw-semibold {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      .completion-chart-track {
        height: 1rem;
        overflow: hidden;
        border-radius: 999px;
        background: #f0e4e7;
      }

      .completion-chart-fill {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--maroon), #b84b5d);
      }

      .completion-chart-metrics {
        min-width: 9rem;
        text-align: right;
        font-variant-numeric: tabular-nums;
      }

      .table td, .table th { vertical-align: middle; }
      .table thead th {
        color: #5c5055;
        background: #fcf8f9;
        border-bottom-color: var(--line);
        font-size: .82rem;
        text-transform: uppercase;
      }

      .table tbody tr:hover { --bs-table-hover-bg: #fff6f7; }

      .report-table-wrap {
        max-height: calc(100vh - 290px);
        min-height: 260px;
      }

      .report-table {
        min-width: 980px;
      }

      .report-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        box-shadow: inset 0 -1px 0 var(--line);
      }

      .report-table th,
      .report-table td {
        padding: .85rem .9rem;
      }

      .report-table .report-student-col {
        left: 0;
        position: sticky;
        z-index: 3;
        min-width: 230px;
        max-width: 280px;
        background: #fff;
        box-shadow: 1px 0 0 var(--line);
      }

      .report-table thead .report-student-col {
        background: #fcf8f9;
        z-index: 4;
      }

      .report-table tbody tr:hover .report-student-col {
        background: #fff6f7;
      }

      .report-score {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 3.15rem;
        padding: .32rem .55rem;
        border-radius: 999px;
        border: 1px solid var(--line);
        background: #fff;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
      }

      .report-score-strong {
        color: #17643a;
        background: #edf9f2;
        border-color: #bfe7cd;
      }

      .report-score-ok {
        color: #6d5200;
        background: #fff7df;
        border-color: #ecd68f;
      }

      .report-score-low {
        color: #9d1c2c;
        background: #fff0f2;
        border-color: #efc4cb;
      }

      .report-score-empty {
        color: var(--muted);
        background: #f7f3f4;
      }

      .report-overall-bar {
        width: 7.5rem;
        height: .42rem;
        overflow: hidden;
        border-radius: 999px;
        background: #f0e4e7;
      }

      .report-overall-bar span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: var(--maroon);
      }

      .report-question-heading {
        display: block;
        max-width: 150px;
        white-space: normal;
        line-height: 1.2;
        text-transform: none;
      }

      .report-row-empty {
        background: #fffdfd;
      }

      .form-control:focus, .form-select:focus {
        border-color: #b84b5d;
        box-shadow: 0 0 0 .2rem rgba(122, 16, 34, .14);
      }

      .btn {
        border-radius: 7px;
        font-weight: 600;
      }

      .btn-success {
        --bs-btn-bg: var(--maroon);
        --bs-btn-border-color: var(--maroon);
        --bs-btn-hover-bg: var(--maroon-dark);
        --bs-btn-hover-border-color: var(--maroon-dark);
        --bs-btn-active-bg: var(--maroon-dark);
        --bs-btn-active-border-color: var(--maroon-dark);
        --bs-btn-disabled-bg: #b98b94;
        --bs-btn-disabled-border-color: #b98b94;
      }

      .btn-outline-success {
        --bs-btn-color: var(--maroon);
        --bs-btn-border-color: var(--maroon);
        --bs-btn-hover-bg: var(--maroon);
        --bs-btn-hover-border-color: var(--maroon);
        --bs-btn-active-bg: var(--maroon-dark);
        --bs-btn-active-border-color: var(--maroon-dark);
      }

      .badge.text-bg-success {
        background-color: var(--maroon) !important;
      }

      .badge.text-bg-light {
        background-color: var(--maroon-soft) !important;
        color: var(--maroon) !important;
        border: 1px solid var(--maroon-border);
      }

      .score-radio input { display: none; }
      .score-radio label {
        min-width: 46px;
        text-align: center;
        border: 1px solid var(--maroon-border);
        border-radius: 6px;
        padding: .45rem .6rem;
        cursor: pointer;
        background: #fff;
      }

      .score-radio label:hover { background: var(--maroon-soft); }
      .score-radio input:checked + label { background: var(--maroon); border-color: var(--maroon); color: #fff; }
      .click-card { transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease; }
      .click-card:hover {
        transform: translateY(-2px);
        border-color: var(--maroon-border) !important;
        box-shadow: 0 16px 38px rgba(84, 9, 22, .12) !important;
      }

      .student-page { max-width: 1120px; }
      .student-hero {
        overflow: hidden;
        background:
          linear-gradient(135deg, rgba(255, 255, 255, .12), rgba(255, 255, 255, 0)),
          linear-gradient(135deg, var(--maroon-dark), var(--maroon));
        color: #fff;
      }

      .student-hero .text-secondary,
      .student-hero .small {
        color: rgba(255, 255, 255, .78) !important;
      }

      .student-hero .progress {
        background: rgba(255, 255, 255, .18);
        height: .75rem;
      }

      .student-hero .progress-bar {
        background: #fff !important;
      }

      .student-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        border: 1px solid rgba(255, 255, 255, .24);
        border-radius: 999px;
        padding: .4rem .7rem;
        color: rgba(255, 255, 255, .9);
        background: rgba(255, 255, 255, .10);
        font-size: .9rem;
      }

      .student-stat {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 1rem;
        height: 100%;
      }

      .student-stat .value {
        font-size: 1.75rem;
        line-height: 1;
        font-weight: 700;
        color: var(--maroon);
      }

      .member-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
      }

      .member-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.75rem;
        height: 2.75rem;
        flex: 0 0 2.75rem;
        border-radius: 999px;
        background: var(--maroon-soft);
        color: var(--maroon);
        font-weight: 700;
      }

      .evaluation-shell { max-width: 900px; }
      .evaluation-header {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 1rem;
      }

      .criterion-card {
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 1rem;
        background: #fff;
      }

      .score-radio {
        display: grid !important;
        grid-template-columns: repeat(5, minmax(44px, 1fr));
        gap: .55rem !important;
      }

      .score-radio label {
        width: 100%;
        min-height: 64px;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 700;
      }

      .score-number {
        font-size: 1.05rem;
        line-height: 1;
      }

      .score-name {
        max-width: 100%;
        margin-top: .25rem;
        font-size: .7rem;
        font-weight: 600;
        line-height: 1.1;
        overflow-wrap: anywhere;
      }

      .evaluation-actions {
        position: sticky;
        bottom: 0;
        z-index: 100;
        padding-top: 1rem;
        background: linear-gradient(180deg, rgba(250, 247, 248, 0), var(--page) 35%);
      }

      .student-nav-dialog {
        position: fixed;
        top: calc(var(--nav-height) + .5rem);
        right: .85rem;
        width: min(300px, calc(100vw - 1.5rem));
        margin: 0;
      }

      .student-nav-dialog .modal-content {
        overflow: hidden;
        border: 1px solid var(--line) !important;
        border-radius: 10px;
        box-shadow: 0 22px 55px rgba(84, 9, 22, .18) !important;
      }

      .student-nav-panel .modal-header {
        padding: 1rem;
        border-bottom: 1px solid var(--line);
        background: #fff;
      }

      .student-nav-panel .modal-body { padding: .65rem; }
      .student-nav-profile {
        display: flex;
        gap: .75rem;
        align-items: center;
      }

      .student-nav-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        width: 100%;
        padding: .78rem .85rem;
        border-radius: 8px;
        color: var(--ink);
        text-decoration: none;
        font-weight: 600;
      }

      .student-nav-item:hover {
        background: var(--maroon-soft);
        color: var(--maroon);
      }

      .student-nav-item.danger:hover {
        background: #fff1f1;
        color: #9d1c2c;
      }

      .student-nav-note {
        padding: .6rem .85rem .75rem;
        color: var(--muted);
        font-size: .82rem;
      }

      .student-nav-modal-backdrop.modal-backdrop.show {
        opacity: .08;
      }

      .auth-page {
        min-height: calc(100vh - var(--nav-height));
        display: grid;
        align-items: center;
        padding-top: 2rem;
        padding-bottom: 2rem;
      }

      .auth-card {
        overflow: hidden;
        border-radius: 12px;
      }

      .auth-aside {
        height: 100%;
        min-height: 420px;
        padding: 2rem;
        background:
          linear-gradient(135deg, rgba(255, 255, 255, .14), rgba(255, 255, 255, 0)),
          linear-gradient(135deg, var(--maroon-dark), var(--maroon));
        color: #fff;
      }

      .auth-aside .text-secondary {
        color: rgba(255, 255, 255, .76) !important;
      }

      .auth-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border-radius: 999px;
        padding: .42rem .75rem;
        background: rgba(255, 255, 255, .12);
        color: rgba(255, 255, 255, .92);
        border: 1px solid rgba(255, 255, 255, .20);
        font-size: .88rem;
      }

      .auth-form-panel { padding: 2rem; }
      .auth-input {
        min-height: 48px;
        border-radius: 8px;
      }

      .auth-helper {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .75rem;
        border-radius: 8px;
        background: var(--maroon-soft);
        color: #5d2530;
        font-size: .9rem;
      }

      .auth-role-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .75rem;
      }

      .auth-role {
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: .85rem;
        background: #fff;
      }

      .auth-role .icon { color: var(--maroon); }

      .site-footer {
        padding: 1rem;
        border-top: 1px solid var(--line);
        background: #fff;
        color: var(--muted);
        font-size: .9rem;
      }

      .landing-page {
        background: #fff;
      }

      .landing-hero {
        position: relative;
        min-height: calc(100vh - var(--nav-height) - 72px);
        display: grid;
        align-items: center;
        overflow: hidden;
        color: #fff;
        background-image:
          linear-gradient(90deg, rgba(38, 16, 22, .90), rgba(64, 21, 31, .70) 42%, rgba(64, 21, 31, .22)),
          url('/assets/landing-hero.png');
        background-size: cover;
        background-position: center;
      }

      .landing-hero-inner {
        width: min(1120px, calc(100% - 2rem));
        margin: 0 auto;
        padding: 4rem 0 5rem;
      }

      .landing-kicker {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .75rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .22);
        background: rgba(255, 255, 255, .12);
        color: rgba(255, 255, 255, .92);
        font-size: .88rem;
        font-weight: 700;
      }

      .landing-title {
        max-width: 700px;
        margin: 1rem 0;
        font-size: clamp(2.35rem, 6vw, 4.75rem);
        line-height: .98;
        font-weight: 800;
      }

      .landing-copy {
        max-width: 590px;
        color: rgba(255, 255, 255, .80);
        font-size: 1.1rem;
      }

      .landing-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .75rem;
        margin-top: 1.5rem;
      }

      .landing-actions .btn-light {
        color: var(--maroon);
        font-weight: 800;
      }

      .landing-overview {
        margin-top: -72px;
        position: relative;
        z-index: 2;
        padding-bottom: 3rem;
      }

      .landing-overview-shell {
        width: min(1120px, calc(100% - 2rem));
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.1fr .9fr;
        gap: 1rem;
      }

      .landing-panel {
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 20px 55px rgba(84, 9, 22, .12);
      }

      .landing-panel-main {
        padding: 1.25rem;
      }

      .landing-flow {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .85rem;
      }

      .landing-flow-step {
        padding: 1rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fffdfd;
      }

      .landing-flow-step .icon {
        color: var(--maroon);
        width: 1.2rem;
        height: 1.2rem;
      }

      .landing-side {
        display: grid;
        gap: .75rem;
        padding: 1rem;
      }

      .landing-side-item {
        display: flex;
        gap: .75rem;
        align-items: flex-start;
        padding: .85rem;
        border-radius: 8px;
        background: #f8fbfa;
        border: 1px solid #dbece5;
      }

      .landing-side-item:nth-child(2) {
        background: #fff9e9;
        border-color: #efe0b4;
      }

      @media (max-width: 991.98px) {
        .app-shell { display: block !important; }
        .site-footer {
          text-align: center;
        }
        .sidebar {
          position: sticky;
          z-index: 1020;
          left: auto;
          bottom: auto;
          width: 100%;
          min-height: auto;
          top: var(--nav-height);
          z-index: 1020;
          padding: .75rem !important;
          overflow-x: auto;
          box-shadow: 0 8px 22px rgba(84, 9, 22, .12);
        }

        .sidebar .fw-semibold { margin-bottom: .6rem !important; }
        .sidebar .nav {
          flex-direction: row !important;
          flex-wrap: nowrap;
          gap: .45rem !important;
          min-width: max-content;
        }

        .sidebar .nav-link {
          white-space: nowrap;
          padding: .55rem .7rem;
        }

        .admin-content {
          margin-left: 0;
          width: 100%;
          flex-basis: auto;
          padding: 1rem !important;
        }

        .landing-hero {
          min-height: auto;
          background-position: 58% center;
        }

        .landing-overview {
          margin-top: -36px;
        }

        .landing-overview-shell {
          grid-template-columns: 1fr;
        }
      }

      @media (max-width: 575.98px) {
        .container, .container-fluid { padding-left: 1rem; padding-right: 1rem; }
        h1, .h3 { font-size: 1.35rem; }
        .card-body { padding: 1rem; }
        .table { min-width: 640px; }
        .report-table-wrap { max-height: none; }
        .report-table .report-student-col {
          position: static;
          min-width: 210px;
          box-shadow: none;
        }
        .block-stat-grid,
        .group-summary {
          grid-template-columns: 1fr;
        }
        .evaluation-member-item,
        .mean-panel {
          align-items: stretch;
          flex-direction: column;
        }
        .completion-chart-row {
          grid-template-columns: 1fr;
          gap: .65rem;
        }
        .completion-chart-metrics {
          min-width: 0;
          text-align: left;
        }
        .btn-lg { --bs-btn-padding-y: .55rem; --bs-btn-padding-x: 1rem; --bs-btn-font-size: 1rem; }
        .member-card {
          align-items: stretch;
          flex-direction: column;
        }

        .member-card .btn {
          width: 100%;
        }

        .student-chip {
          width: 100%;
          justify-content: center;
        }

        .evaluation-actions .d-flex {
          flex-direction: column-reverse;
          gap: .75rem;
        }

        .evaluation-actions .btn {
          width: 100%;
        }

        .auth-page {
          align-items: start;
          padding-top: 1rem;
        }

        .auth-aside {
          min-height: auto;
          padding: 1.25rem;
        }

        .auth-form-panel {
          padding: 1.25rem;
        }

        .auth-role-row {
          grid-template-columns: 1fr;
        }
        .top-profile-meta {
          display: none;
        }
        .landing-hero-inner {
          padding: 3rem 0 4rem;
        }
        .landing-flow {
          grid-template-columns: 1fr;
        }
        .landing-actions .btn {
          width: 100%;
        }
      }
    </style>
  </head>
  <body>
    @include('include.header')
    <main>
      @if(session('success') && !session('profile_modal') && !session('student_profile_modal'))
        <div class="container-fluid mt-3">
          <div class="alert alert-success mb-0">{{ session('success') }}</div>
        </div>
      @endif

      @if(session('error'))
        <div class="container-fluid mt-3">
          <div class="alert alert-danger mb-0">{{ session('error') }}</div>
        </div>
      @endif

      @if($errors->any())
        <div class="container-fluid mt-3">
          <div class="alert alert-danger mb-0">
            <strong>Please check the form.</strong>
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      @yield('content')
    </main>
    <footer class="site-footer">
      <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>Peer Evaluation System</span>
        <span>&copy; {{ date('Y') }} Kevin C. Astillero. All rights reserved.</span>
      </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
      if (window.lucide) {
        lucide.createIcons();
      }
    </script>
    @stack('scripts')
  </body>
</html>
