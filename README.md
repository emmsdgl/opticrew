# OptiCrew - AI-Powered Workforce Optimization Platform

**OptiCrew** (Fin-noys) is an intelligent workforce optimization and scheduling system designed for cleaning service management companies. The platform uses a **Genetic Algorithm** combined with rule-based optimization to automatically assign tasks to employees while respecting constraints like availability, skills, work hours, and budget limits.

---

## 🌟 Key Features

### Intelligent Scheduling
- **Genetic Algorithm Optimization** - Multi-generation population-based task scheduling
- **Rule-Based Preprocessing** - Validates tasks and filters employees before optimization
- **Real-Time Task Management** - Add tasks to existing schedules on-the-fly
- **Scenario Analysis** - What-if planning for emergency tasks, employee absences, and more

### Workforce Management
- **Employee Profiles** - Skills, licenses, efficiency ratings, hourly wages
- **Dynamic Team Formation** - Automatic team creation based on task requirements
- **Geofenced Attendance** - GPS-based clock-in/clock-out tracking
- **Performance Metrics** - Duration accuracy, completion rates, efficiency scores

### Client Management
- **Multi-Client Support** - Contracted clients and individual customers
- **Online Appointment Booking** - Service request forms with automatic scheduling
- **Client Portal** - View service history, status, and feedback
- **Feedback System** - Client satisfaction surveys

### Real-Time Alerts
- **Task Monitoring** - Alerts when tasks exceed estimated time or go on hold
- **Performance Flags** - Automatic detection of underperforming tasks
- **Employee Absence Alerts** - Notify admins of unexpected absences
- **Alert Management Dashboard** - Acknowledge and track all system alerts

### AI Chatbot
- **Dual AI Provider Support** - Google Gemini (free tier) or Claude API (production)
- **Company Knowledge Base** - Context-aware responses about Fin-noys services
- **Rate Limited** - Prevents API abuse (15-30 requests/minute)
- **Multi-Turn Conversations** - Maintains chat history

### Reporting & Analytics
- **Admin Dashboard** - Real-time metrics, attendance rates, task overview
- **Employee Analytics** - Performance trends, efficiency scores, payroll data
- **Client Reports** - Service completion, revenue tracking
- **Export Functionality** - CSV/Excel exports for further analysis

---

## 🛠️ Technology Stack

### Backend
- **Laravel 9.x** - PHP web application framework
- **PHP 8.0+** - Server-side language
- **MySQL 5.7+** - Relational database

### Frontend
- **Livewire 2.x** - Real-time reactive components (no page reloads)
- **Tailwind CSS 3.x** - Utility-first CSS framework
- **Alpine.js 3.x** - Lightweight JavaScript framework
- **Flowbite** - Tailwind component library
- **Vite 4.x** - Build tool for assets

### AI & Optimization
- **Google Gemini API** - AI chatbot (free testing tier)
- **Claude API (Anthropic)** - Alternative AI provider (production)
- **Custom Genetic Algorithm** - PHP implementation for task optimization

### Authentication & Security
- **Laravel Sanctum** - API token authentication
- **Laravel Breeze** - Authentication scaffolding
- **Custom RBAC Middleware** - Role-based access control

---

## 📁 Project Structure

```
opticrew/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # 32 controllers (Admin, Auth, API, Employee, Client)
│   │   ├── Middleware/           # RBAC, Auth, CSRF protection
│   │   └── Livewire/             # Real-time components
│   ├── Models/                   # 21 Eloquent models
│   ├── Services/
│   │   ├── Optimization/         # Genetic Algorithm, preprocessing, scenarios
│   │   ├── Notification/         # Alert management
│   │   ├── Team/                 # Team formation & efficiency
│   │   └── Workforce/            # Workforce calculations
│   └── Policies/                 # Authorization policies
├── database/
│   ├── migrations/               # 36 migration files
│   └── seeders/                  # Database seeders
├── resources/
│   ├── views/                    # Blade templates (admin, employee, client, landing)
│   └── lang/                     # Localization files
├── routes/
│   ├── web.php                   # Web routes (main app)
│   ├── api.php                   # API routes
│   └── auth.php                  # Authentication routes
└── storage/                      # Logs, cache, uploaded files
```

---

## 🚀 Installation

### Prerequisites
- PHP 8.0 or higher
- Composer
- MySQL 5.7 or higher
- Node.js & NPM (for frontend assets)
- XAMPP/WAMP/MAMP (for local development)

### Setup Instructions

1. **Clone the repository**
   ```bash
   cd C:\xampp\htdocs
   git clone <repository-url> opticrew
   cd opticrew
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database** (Edit `.env`)
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=opticrew
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed database** (Optional - for demo data)
   ```bash
   php artisan db:seed
   ```

7. **Configure AI Provider** (Edit `.env`)
   ```env
   # Choose: 'gemini' (free) or 'claude' (production)
   AI_PROVIDER=gemini

   # Get free key from: https://aistudio.google.com/app/apikey
   GEMINI_API_KEY=your-api-key-here
   ```

8. **Build frontend assets**
   ```bash
   npm run dev
   ```

9. **Start the server**
   ```bash
   php artisan serve
   ```

10. **Access the application**
    - URL: `http://localhost:8000`
    - Admin Login: Create via seeder or register manually

---

## 👥 User Roles

### Admin
- Full system access
- Task creation and scheduling
- Run optimization algorithms
- View all reports and analytics
- Manage user accounts
- Configure system settings

### Employee
- View assigned tasks for the day
- Clock-in/clock-out with geofencing
- Start, hold, and complete tasks
- View personal performance metrics
- Update profile information

### External Client
- Book cleaning appointments online
- View service history
- Check appointment status
- Provide feedback/reviews
- Manage account settings

---

## 🧬 Genetic Algorithm Configuration

The optimization engine can be fine-tuned in `.env`:

```env
# Genetic Algorithm Parameters
GA_POPULATION_SIZE=10          # Population size per generation
GA_MAX_GENERATIONS=100         # Maximum generations
GA_MUTATION_RATE=0.1           # Mutation probability (0-1)
GA_TOURNAMENT_SIZE=5           # Tournament selection size
GA_PATIENCE=15                 # Early stopping patience

# Workforce Constraints
WORKFORCE_BUDGET_LIMIT=10000   # Maximum budget per day
WORK_START_TIME=08:00:00       # Work day start
WORK_END_TIME=22:00:00         # Work day end
```

---

## 📚 Documentation

Comprehensive documentation is available in the project:

- **[API Documentation](API_DOCUMENTATION.md)** - Complete API specification with examples
- **[RBAC Guide](ROLE_BASED_ACCESS_CONTROL.md)** - Role-based access control implementation
- **[Chatbot Setup](CHATBOT_SETUP_GUIDE.md)** - Gemini API integration guide
- **[Claude Setup](CLAUDE_CHATBOT_SETUP.md)** - Claude API setup instructions
- **[AI Provider Switching](AI_PROVIDER_SWITCHING_GUIDE.md)** - Switch between Gemini and Claude
- **[Testing Guide](TESTING_GUIDE.md)** - System testing procedures
- **[Optimization Test Plan](OPTIMIZATION_TEST_PLAN.md)** - Testing strategy for GA

---

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

---

## 🔧 Configuration Files

Key configuration files:

| File | Purpose |
|------|---------|
| `.env` | Environment variables (database, API keys, GA params) |
| `config/auth.php` | Authentication guards and providers |
| `config/optimization.php` | Genetic Algorithm parameters and constraints |
| `config/database.php` | Database connections |
| `config/sanctum.php` | API token authentication |

---

## 🔒 Security Features

- **Role-Based Access Control (RBAC)** - Middleware enforces role-based routes
- **CSRF Protection** - Laravel CSRF token validation on all forms
- **Password Hashing** - Bcrypt hashing for user passwords
- **Security Questions + OTP** - Two-factor password recovery
- **API Rate Limiting** - Prevents abuse on sensitive endpoints
- **Soft Deletes** - Audit trail for critical records
- **Input Validation** - Form request validation throughout

---

## 🌐 API Endpoints

### Authentication
- `POST /api/user` - Get authenticated user

### Employee Dashboard
- `GET /api/employee/tasks` - Get team's tasks for a date
- `POST /api/tasks/{id}/start` - Start a task
- `POST /api/tasks/{id}/hold` - Put task on hold
- `POST /api/tasks/{id}/complete` - Complete a task

### Admin Dashboard
- `GET /api/admin/alerts/unacknowledged` - Get unread alerts
- `POST /api/admin/alerts/{id}/acknowledge` - Mark alert as read

### Chatbot
- `POST /api/chatbot/message` - Send message to AI chatbot (public, rate limited)

---

## 📊 Database Schema

30 tables including:

- **users** - Authentication and base user data
- **employees** - Employee profiles with skills and efficiency
- **clients** - External client profiles
- **tasks** - Service tasks to be completed
- **optimization_runs** - GA optimization session records
- **optimization_teams** - Team assignment results
- **attendances** - Clock-in/out records with geofencing
- **alerts** - System alerts for task delays
- **notifications** - User notifications
- **performance_flags** - Tasks that exceeded estimates

---

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set strong database password
- [ ] Rotate API keys (Gemini, Claude)
- [ ] Configure proper email provider
- [ ] Enable Redis for caching
- [ ] Set up SSL certificate
- [ ] Configure automated backups
- [ ] Set up error monitoring (Sentry, etc.)
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`

---

## 📝 License

[Add your license information here]

---

## 👨‍💻 Development Team

**Project**: OptiCrew (Fin-noys)
**Purpose**: AI-powered workforce optimization for cleaning services

---

## 🆘 Support

For issues, questions, or contributions, please contact the development team or create an issue in the project repository.

---

**Built with ❤️ using Laravel**
