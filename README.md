# 🚀 DevVault - GitHub Portfolio Manager

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue 3">
  <img src="https://img.shields.io/badge/Filament-3-F59E0B?style=for-the-badge&logo=data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMiAyMkgyMkwxMiAyWiIgZmlsbD0id2hpdGUiLz4KPC9zdmc+" alt="Filament 3">
  <img src="https://img.shields.io/badge/Inertia.js-1.0-9553E9?style=for-the-badge&logo=inertia&logoColor=white" alt="Inertia.js">
  <img src="https://img.shields.io/badge/TailwindCSS-3-06B6D4?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
</p>

<p align="center">
  A modern GitHub portfolio showcase and management system with real-time GitHub API integration, contribution tracking, and admin dashboard powered by Filament.
</p>

---

## ✨ Features

### 🎨 Public Portfolio

-   **Modern UI/UX** - Beautiful, responsive design showcasing your GitHub repositories
-   **Real-time GitHub Sync** - Automatically fetches and displays repositories from GitHub API
-   **Advanced Filtering** - Search by name/description and filter by programming language
-   **Multiple View Modes** - Switch between grid and list layouts
-   **Language Distribution Chart** - Visual representation of your tech stack using Chart.js
-   **Repository Stats** - Display stars, forks, topics, and descriptions
-   **Smooth Animations** - Hover effects and transitions for better UX

### 🛠️ Admin Dashboard (Filament)

-   **GitHub Contributions Heatmap** - Visual calendar showing your daily contributions (like GitHub's contribution graph)
-   **Year Filter** - View contributions from any year (2020 onwards)
-   **Quick Stats Cards** - Total repositories, stars, forks, and active projects
-   **Project Management** - CRUD operations for managing tracked repositories
-   **User Management** - Built-in authentication and user management
-   **Dark Mode Support** - Automatic theme switching

### ⚡ Technical Features

-   **Caching System** - Smart caching with Laravel Cache for optimal performance
-   **GitHub GraphQL API** - Fetch detailed contribution data
-   **Responsive Design** - Mobile-first approach with Tailwind CSS
-   **SEO Optimized** - Meta tags and proper heading structure
-   **Code Quality** - Following Laravel best practices and SOLID principles

---

## 🛠️ Tech Stack

-   **Backend:** Laravel 12
-   **Frontend:** Vue 3 + Inertia.js
-   **Admin Panel:** Filament 3
-   **Styling:** Tailwind CSS
-   **Charts:** Chart.js
-   **API:** GitHub REST API & GraphQL API
-   **Database:** MySQL/PostgreSQL
-   **Caching:** Redis/File Cache

---

## 📋 Prerequisites

-   PHP 8.2 or higher
-   Composer
-   Node.js & NPM
-   MySQL/PostgreSQL
-   Redis (optional, for caching)
-   GitHub Personal Access Token

---

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/devvault.git
cd devvault
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=devvault
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Configure GitHub API

Add your GitHub credentials to `.env`:

```env
GITHUB_TOKEN=your_github_personal_access_token
GITHUB_USERNAME=your_github_username
```

**Creating GitHub Token:**

1. Go to GitHub Settings → Developer settings → Personal access tokens
2. Generate new token with scopes: `repo`, `read:user`, `read:org`
3. Copy the token to your `.env` file

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Create Admin User

```bash
php artisan make:filament-user
```

### 9. Build Assets

```bash
npm run build
# or for development
npm run dev
```

### 10. Start Development Server

```bash
php artisan serve
```

Visit:

-   **Public Portfolio:** http://localhost:8000
-   **Admin Dashboard:** http://localhost:8000/admin

---

## 📱 Usage

### Public Portfolio

Navigate to the homepage to see your GitHub portfolio with all repositories displayed in an interactive grid/list layout.

**Features:**

-   Search repositories by name or description
-   Filter by programming language
-   Toggle between grid and list views
-   Click on any repository card to visit GitHub

### Admin Dashboard

Access the admin panel at `/admin` using your admin credentials.

**Available Sections:**

1. **Dashboard** - Overview with contribution heatmap and stats
2. **Projects** - Manage tracked repositories
3. **Users** - User management

**Sync Repositories:**
Repositories are automatically fetched from GitHub API and cached for 1 hour. To force refresh, clear the cache:

```bash
php artisan cache:clear
```

---

## 🎨 Customization

### Branding

Edit `AdminPanelProvider.php`:

```php
->brandName('Your Brand Name')
->colors([
    'primary' => Color::Amber, // Change primary color
])
```

### Cache Duration

Edit `GitHubService.php` to change cache duration:

```php
Cache::remember('github_repos', 3600, function () { // 3600 = 1 hour
    // ...
});
```

### UI Theme

Modify Tailwind config in `tailwind.config.js` to customize colors, fonts, etc.

---

## 🧪 Testing

Run tests with Pest:

```bash
php artisan test
```

---

## 📦 Project Structure

```
devvault/
├── app/
│   ├── Filament/
│   │   ├── Resources/      # Filament resources (CRUD)
│   │   └── Widgets/        # Dashboard widgets
│   ├── Http/
│   │   └── Controllers/    # Controllers
│   ├── Models/             # Eloquent models
│   └── Services/           # Business logic (GitHubService)
├── resources/
│   ├── js/
│   │   ├── Components/     # Vue components
│   │   ├── Layouts/        # Layout components
│   │   └── Pages/          # Inertia pages
│   └── views/              # Blade views
├── routes/
│   └── web.php            # Web routes
└── database/
    └── migrations/         # Database migrations
```

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Author

Built with ❤️ by [Your Name]

---

## 🙏 Acknowledgments

-   [Laravel](https://laravel.com) - The PHP Framework
-   [Filament](https://filamentphp.com) - Admin Panel Builder
-   [Inertia.js](https://inertiajs.com) - Modern Monolith Bridge
-   [Vue.js](https://vuejs.org) - Progressive JavaScript Framework
-   [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS Framework
-   [GitHub API](https://docs.github.com/en/rest) - Repository Data Source

---

## 📧 Support

If you have any questions or need help, please open an issue or contact me at [your-email@example.com]

---

<p align="center">Made with ☕ and 💻</p>
