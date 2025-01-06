# Laravel Simple Exam Application 📝

A web-based examination system built with Laravel 11, featuring user management, exam scheduling, and automated grading.

## Screenshots 📸

<div align="center">


![Dashboard](https://i.ibb.co.com/BBVFhrr/Screenshot-2025-01-06-at-11-18-04.png)

![Question Edit](https://i.ibb.co.com/n3bFBPx/Screenshot-2025-01-06-at-11-16-51.png)

![Exam Start](https://i.ibb.co.com/K5PPgx3/Screenshot-2025-01-06-at-11-29-58.png)

![Exam Result](https://i.ibb.co.com/LpkHb0C/Screenshot-2025-01-06-at-11-35-41.png)


![Exam Result Manage](https://i.ibb.co.com/R701wQp/Screenshot-2025-01-06-at-11-39-11.png)

![Exam Detail](https://i.ibb.co.com/WvnMLFk/Screenshot-2025-01-06-at-11-41-19.png)

</div>

## Features 🚀

- 👥 User authentication and group management
- 📚 Topic and question management
- 📋 Multiple exam types with configurable settings
- 🔄 Question randomization
- ⏱️ Real-time exam taking
- ✅ Automated grading system
- 📝 Support for multiple question types including essays
- 🖼️ Image support for questions and answers

## Tech Stack 💻

- Laravel 11
- Breeze Authentication
- TailwindCSS
- jQuery
- MySQL/PostgreSQL

## Database Schema 📊

The application uses the following key tables:
- `users`: User management
- `groups`: User grouping for exam access
- `topics`: Question categories
- `questions`: Exam questions with support for images
- `answers`: Answer options for questions
- `exams`: Exam configuration and scheduling
- `exam_results`: Student exam attempts and scores
- `exam_result_details`: Detailed response tracking

## Installation ⚙️

```bash
# Clone the repository
git clone https://github.com/freditrihandoko/laravel-simple-exam.git

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Configure storage symbolic link
php artisan storage:link

# Run migrations and seed database
php artisan migrate --seed

# Compile assets
npm run dev

# Start the server
php artisan serve
```

## Environment Requirements 🛠️

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL 8.0+ or PostgreSQL 13+

## License 📄

[MIT License](LICENSE.md)

## Contributing 🤝

1. Fork the repository
2. Create a feature branch
3. Commit changes
4. Push to the branch
5. Create a Pull Request
