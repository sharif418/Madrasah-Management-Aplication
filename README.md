# মাদরাসা ম্যানেজমেন্ট অ্যাপ্লিকেশন
## Madrasah Management Application

একটি সম্পূর্ণ মাদরাসা পরিচালনার জন্য Premium Quality Laravel ওয়েব অ্যাপ্লিকেশন।

## 🚀 Features

- ✅ 20টি মডিউল (Students, Teachers, Fees, Exams, Library, Hostel, etc.)
- ✅ 3টি Portal (Admin, Student, Parent)
- ✅ পাবলিক ওয়েবসাইট
- ✅ 30+ PDF Templates
- ✅ Role-based Access Control
- ✅ Bengali Language Support

## 📋 Requirements

- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer

## 🔧 Installation

### Local Development

```bash
# Clone repository
git clone https://github.com/sharif418/Madrasah-Management-Aplication.git
cd Madrasah-Management-Aplication

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env file

# Run migrations and seeders
php artisan migrate --seed

# Build assets
npm run build

# Start development server
php artisan serve
```

### Production (Coolify/Docker)

The application includes a Dockerfile for easy deployment.

```bash
# Build and deploy using Docker
docker build -t madrasah-management .
docker run -p 80:80 madrasah-management
```

## 🔑 Default Login

- **Email:** admin@madrasah.com
- **Password:** password

## 📁 Directory Structure

```
├── app/
│   ├── Filament/          # Admin panel resources
│   │   ├── Resources/     # CRUD resources
│   │   ├── Pages/         # Custom pages
│   │   ├── Widgets/       # Dashboard widgets
│   │   ├── Parent/        # Parent portal
│   │   └── Student/       # Student portal
│   ├── Models/            # Eloquent models
│   └── Http/Controllers/  # Controllers
├── resources/
│   └── views/
│       ├── website/       # Public website
│       ├── pdf/           # PDF templates
│       └── filament/      # Filament views
└── database/
    ├── migrations/        # Database migrations
    └── seeders/           # Database seeders
```

## 🌐 URLs

| Portal | URL | Access |
|--------|-----|--------|
| Website | `/` | Public |
| Admin | `/admin` | Staff |
| Student | `/student` | Students |
| Parent | `/parent` | Parents |

## 📜 License

MIT License

## 👨‍💻 Developer

Made with ❤️ for Madrasah Management
