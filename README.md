# 📡 Calculadora de Pérdida Óptica FTTH

Una aplicación web moderna y escalable para calcular pérdidas de potencia óptica en redes de fibra hasta el hogar (FTTH), desarrollada con Laravel 11, Livewire 3 y Tailwind CSS.

## 🚀 Características Principales

### ✨ Funcionalidades
- **Cálculo en tiempo real** de pérdidas ópticas usando Livewire
- **Interfaz responsiva y moderna** con Tailwind CSS
- **Configuración flexible de splitters** (1:2, 1:4, 1:8, 1:16, 1:32, 1:64)
- **Parámetros configurables** para fusiones, acopladores y fibra óptica
- **Visualización del desglose detallado** de cada componente
- **Diagrama visual de la red** con indicadores de pérdida
- **Validación automática** con advertencias para potencias fuera de rango
- **Configuraciones predefinidas** para escenarios comunes
- **Arquitectura escalable y modular**

### 🔧 Parámetros de Entrada
- **Potencia de entrada (dBm)**: Potencia inicial del equipo OLT
- **Splitters**: Tipo, cantidad y pérdidas personalizables
- **Fusiones**: Cantidad y pérdida por empalme (típicamente 0.03 dB)
- **Acopladores**: Cantidad y pérdida por conector (típicamente 0.5 dB)
- **Distancia de fibra**: Longitud en kilómetros
- **Atenuación de fibra**: Pérdida por kilómetro (típicamente 0.2 dB/km)
- **Margen de diseño**: Factor de seguridad adicional

### 📊 Salidas del Sistema
- **Pérdida total acumulada** en dB
- **Potencia de salida final** en dBm
- **Desglose detallado** por componente
- **Diagrama visual** de la red
- **Indicadores de estado** con códigos de color
- **Advertencias automáticas** para valores fuera de rango

## 🏗️ Arquitectura

La aplicación sigue principios **SOLID** y patrones de diseño modular:

### 📁 Estructura del Proyecto
```
app/
├── DTOs/                           # Data Transfer Objects
│   ├── OpticalLossCalculationDto.php
│   └── OpticalLossResultDto.php
├── Services/                       # Lógica de negocio
│   └── OpticalLossService.php
├── Livewire/                      # Componentes reactivos
│   └── OpticalLossCalculator.php
├── Models/                        # Modelos de datos
│   └── OpticalConfiguration.php
└── Providers/                     # Service Providers
    └── OpticalLossServiceProvider.php
```

### 🧩 Componentes Principales

#### 1. **OpticalLossService**
- Servicio principal para cálculos de pérdida óptica
- Implementa el principio de responsabilidad única (SRP)
- Métodos especializados para cada tipo de componente
- Validación automática de resultados

#### 2. **DTOs (Data Transfer Objects)**
- `OpticalLossCalculationDto`: Encapsula parámetros de entrada
- `OpticalLossResultDto`: Estructura de resultados calculados
- Tipado fuerte y validación de datos

#### 3. **Componente Livewire**
- Reactivo y en tiempo real
- Separación entre lógica de presentación y negocio
- Interfaz intuitiva con validación de entrada

#### 4. **Modelo de Configuraciones**
- Almacenamiento de configuraciones predefinidas
- Sistema de plantillas para escenarios comunes
- Historial de cálculos realizados

## 🛠️ Instalación y Configuración

### Prerrequisitos
- PHP 8.2 o superior
- Composer
- Node.js y pnpm
- SQLite (incluido por defecto)

### Pasos de Instalación

1. **Clonar e instalar dependencias**:
```bash
cd C:\xampp\htdocs\redes-proyecto
composer install
pnpm install
```

2. **Configurar entorno**:
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configurar base de datos**:
```bash
php artisan migrate
php artisan db:seed --class=OpticalConfigurationSeeder
```

4. **Compilar assets e iniciar servidores**:
```bash
# Terminal 1: Compilador de assets
pnpm run dev

# Terminal 2: Servidor Laravel  
php artisan serve
```

5. **Acceder a la aplicación**:
   - Abrir navegador en `http://localhost:8000`

## 💡 Uso de la Aplicación

### Cálculo Básico
1. Introduzca la **potencia de entrada** del equipo OLT
2. Configure los **splitters** necesarios (puede agregar múltiples)
3. Ajuste parámetros de **fusiones, acopladores y fibra**
4. Los resultados se actualizan **automáticamente en tiempo real**
5. Revise el **desglose detallado** y **diagrama visual**

### Funciones Avanzadas
- **Splitters personalizados**: Active "usar pérdida personalizada" para valores específicos
- **Configuraciones predefinidas**: Use plantillas para escenarios comunes
- **Validación automática**: El sistema alertará sobre potencias fuera de rango típico
- **Reseteo rápido**: Botón para volver a valores por defecto

## 📈 Extensibilidad

### Agregar Nuevos Tipos de Componentes
1. Extender `OpticalLossService` con nuevos métodos de cálculo
2. Actualizar DTOs con nuevos parámetros
3. Modificar componente Livewire para nuevos campos
4. Actualizar vista con controles adicionales

### Integración con APIs Externas
```php
// Ejemplo: Servicio para obtener datos de equipos
class EquipmentApiService {
    public function getOpticalTransmitterPower($equipment_id) {
        // Lógica para consultar API externa
    }
}
```

### Reportes y Exportación
```php
// Ejemplo: Servicio de reportes
class OpticalReportService {
    public function generatePDFReport(OpticalLossResultDto $result) {
        // Generar reporte en PDF
    }
}
```

## 🔬 Rangos Típicos FTTH

### Potencias de Referencia
- **OLT (Transmisión)**: 0 a +8 dBm
- **ONT (Recepción)**: -8 a -28 dBm
- **Rango óptimo**: -8 a -25 dBm

### Pérdidas Típicas por Componente
- **Splitter 1:2**: 3.2 dB
- **Splitter 1:4**: 7.2 dB  
- **Splitter 1:8**: 11.2 dB
- **Splitter 1:16**: 14.0 dB
- **Fusión**: 0.03 dB
- **Conector**: 0.5 dB
- **Fibra**: 0.2 dB/km

## 🧪 Testing

```bash
# Ejecutar tests unitarios
php artisan test

# Tests específicos del servicio
php artisan test --filter OpticalLossServiceTest
```

## 🚀 Despliegue en Producción

### Preparación
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
pnpm run build
```

### Variables de Entorno
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
```

## 📊 Métricas y Monitoreo

La aplicación está preparada para integrar:
- **Laravel Telescope**: Debug y profiling
- **Laravel Horizon**: Monitoreo de colas
- **Logging personalizado**: Registro de cálculos y errores

## 🤝 Contribución

### Estructura de Commits
- `feat:` Nueva funcionalidad
- `fix:` Corrección de bugs
- `refactor:` Mejora de código
- `docs:` Documentación
- `test:` Nuevos tests

### Estilo de Código
- **PSR-12** para PHP
- **snake_case** para variables
- **Principios SOLID**
- Comentarios descriptivos en español

## 📄 Licencia

Este proyecto está bajo la licencia MIT.

## 👥 Créditos

Desarrollado para ingenieros de telecomunicaciones especializados en redes FTTH.

---

**¿Necesitas ayuda?** Consulta la documentación técnica en `/docs` o contacta al equipo de desarrollo.