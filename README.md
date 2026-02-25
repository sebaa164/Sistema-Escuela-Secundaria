# Sistema de Gestión Académica - I.E.S Normal Superior

<p align="center">
    <img src="https://img.icons8.com/color/96/000000/school.png" alt="Sistema Educativo">
</p>

<p align="center">
    <strong>Sistema Integral de Gestión Académica</strong><br>
    Para Instituciones Educativas de Nivel Secundario
</p>

## Descripción Breve

Sistema web completo para la gestión académica de instituciones educativas, desarrollado en Laravel 8. Permite administrar calificaciones, asistencias, inscripciones, reportes y seguimiento del rendimiento estudiantil. Cuenta con roles diferenciados para administradores, profesores y estudiantes, con interfaces optimizadas para cada perfil de usuario.

## Características Principales

- **Gestión de Usuarios**: Administración de estudiantes, profesores y tutores
- **Control de Calificaciones**: Registro y seguimiento de notas por asignatura
- **Sistema de Asistencias**: Control diario de presencia de estudiantes
- **Inscripciones Online**: Gestión de inscripciones a cursos y secciones
- **Reportes PDF**: Generación automática de boletines y estadísticas
- **Dashboard Analítico**: Visualización de indicadores académicos
- **Gestión de Períodos Académicos**: Configuración de ciclos lectivos
- **Sistema de Evaluaciones**: Tipos variados de evaluaciones y porcentajes

## Credenciales de Acceso

**Administrador**
- **Email**: `admin@sistema.edu`
- **Password**: `password`
- **Permisos**: Acceso completo a todas las funcionalidades del sistema

**Profesor**
- **Email**: `profesor@test.com`
- **Password**: `password`
- **Permisos**: Gestión de calificaciones, asistencias y reportes de sus asignaturas

**Estudiante**
- **Email**: `estudiante@test.com`
- **Password**: `password`
- **Permisos**: Consulta de calificaciones, horarios y progreso académico

## Estructura de Carpetas

```
sistema-escuela/
├── app/
│   ├── Console/              # Comandos de Artisan
│   ├── Exceptions/           # Manejo de excepciones
│   ├── Helpers/             # Funciones auxiliares
│   ├── Http/
│   │   ├── Controllers/     # Controladores MVC
│   │   ├── Middleware/      # Middleware de autenticación
│   │   └── Kernel.php       # Configuración de HTTP
│   ├── Models/              # Modelos Eloquent
│   └── Providers/           # Service Providers
├── bootstrap/               # Archivos de inicialización
├── config/                  # Configuración de la aplicación
├── database/
│   ├── factories/          # Factorias para datos de prueba
│   ├── migrations/         # Migraciones de base de datos
│   └── seeders/            # Datos iniciales
├── public/                 # Archivos públicos (CSS, JS, imágenes)
├── resources/
│   ├── css/               # Hojas de estilo
│   ├── js/                # Archivos JavaScript
│   ├── lang/              # Archivos de idioma
│   └── views/             # Plantillas Blade
│       ├── admin/         # Vistas del panel administrativo
│       ├── auth/          # Vistas de autenticación
│       ├── estudiante/    # Vistas del portal estudiantil
│       ├── profesor/      # Vistas del portal de profesores
│       └── layouts/       # Plantillas base
├── routes/                # Definición de rutas
├── storage/               # Archivos generados por la aplicación
├── tests/                 # Pruebas unitarias y de integración
└── vendor/                # Dependencias de Composer
```

## Dependencias Principales

**Framework y Core**
- **Laravel 8.75**: Framework PHP principal
- **PHP 7.3+**: Lenguaje de programación requerido

**Paquetes Esenciales**
- **barryvdh/laravel-dompdf**: Generación de documentos PDF
- **doctrine/dbal**: Manipulación avanzada de base de datos
- **fruitcake/laravel-cors**: Manejo de CORS
- **guzzlehttp/guzzle**: Cliente HTTP
- **laravel/sanctum**: Autenticación API
- **laravel/tinker**: Consola interactiva
- **phpoffice/phpspreadsheet**: Manipulación de archivos Excel

**Dependencias de Desarrollo**
- **facade/ignition**: Depuración de errores
- **fakerphp/faker**: Generación de datos falsos
- **laravel/sail**: Entorno Docker
- **mockery/mockery**: Framework de mocking
- **nunomaduro/collision**: Manejo de errores en consola
- **phpunit/phpunit**: Framework de testing

## Instalación y Configuración

**Requisitos Previos**
- PHP >= 7.3
- Composer
- MySQL/MariaDB
- Servidor web (Apache/Nginx)

**Pasos de Instalación**

1. **Clonar el repositorio**
   ```bash
   git clone <repositorio-url>
   cd sistema-escuela
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   npm install
   ```

3. **Configurar entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurar base de datos**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=calificacion
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Importar base de datos**
   ```bash
   mysql -u root -p calificacion < calificacion.sql
   ```

6. **Ejecutar seeders**
   ```bash
   php artisan db:seed
   ```

7. **Iniciar servidor**
   ```bash
   php artisan serve
   ```

8. **Compilar assets**
   ```bash
   npm run dev
   ```

## Base de Datos

**Tablas Principales**
- `usuarios`: Gestión de usuarios del sistema
- `periodos_academicos`: Configuración de ciclos lectivos
- `cursos`: Catálogo de asignaturas
- `secciones`: Grupos y horarios de cursos
- `inscripciones`: Registro de estudiantes en secciones
- `calificaciones`: Notas de estudiantes
- `asistencias`: Control de presencia
- `evaluaciones`: Configuración de pruebas y trabajos
- `configuraciones`: Parámetros del sistema

## Interfaces de Usuario

**Panel Administrativo**
- Dashboard con estadísticas generales
- Gestión completa de usuarios y cursos
- Configuración del sistema
- Reportes avanzados

**Portal de Profesores**
- Gestión de calificaciones por asignatura
- Control de asistencias
- Generación de reportes PDF
- Visualización de estudiantes inscritos

**Portal de Estudiantes**
- Consulta de calificaciones
- Visualización de horarios
- Seguimiento del progreso académico
- Descarga de boletines

## Seguridad

- Autenticación basada en sesiones
- Encriptación de contraseñas con bcrypt
- Middleware de protección CSRF
- Validación de inputs del lado del servidor
- Roles y permisos diferenciados

## Reportes Disponibles

- **Boletín de calificaciones** (PDF)
- **Reporte de asistencias** (PDF)
- **Listado de estudiantes** (PDF/Excel)
- **Rendimiento académico** (PDF)
- **Estadísticas generales** (Dashboard)

## Tecnologías Utilizadas

- **Backend**: Laravel 8, PHP 8
- **Frontend**: Bootstrap 5, Font Awesome, JavaScript
- **Base de Datos**: MySQL/MariaDB
- **PDF Generation**: DomPDF
- **Icons**: Font Awesome 6
- **CSS Framework**: Bootstrap 5

## Características Técnicas

- **Responsive Design**: Adaptable a móviles y tablets
- **Dark Mode**: Interfaz oscura opcional
- **Real-time Updates**: Actualizaciones en tiempo real
- **Export Options**: PDF y Excel
- **Multi-language**: Soporte para español (extensible)

## Mantenimiento

**Comandos Útiles**
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimizar aplicación
php artisan config:cache
php artisan route:cache

# Ejecutar migraciones
php artisan migrate:fresh --seed
```

## Notas de Desarrollo

- El sistema utiliza el patrón MVC
- Implementa Eloquent ORM para la gestión de datos
- Usa Blade para las plantillas
- Incluye middleware personalizados para autenticación
- Implementa eventos y listeners para auditoría

## Contribución

1. Fork del proyecto
2. Crear feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit de cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push al branch (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

## Licencia

Este proyecto está licenciado bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para detalles.

## Soporte

Para soporte técnico o reporte de issues, contactar al equipo de desarrollo o crear un issue en el repositorio.

---

**Desarrollado con ❤️ para I.E.S Normal Superior**
