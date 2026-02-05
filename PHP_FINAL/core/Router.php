<?php

namespace Core;


/**
 * كلاس Router لتوجيه الطلبات
 */
class Router
{
    private array $routes = [];
    
    /**
     * إضافة مسار GET
     * 
     * @param string $path
     * @param string $controller
     * @param string $method
     */
    public function get(string $path, string $controller, string $method): void
    {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }
    
    /**
     * إضافة مسار POST
     * 
     * @param string $path
     * @param string $controller
     * @param string $method
     */
    public function post(string $path, string $controller, string $method): void
    {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }
    
    /**
     * تشغيل Router
     */
    public function run(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // معالجة تشغيل المشروع في مجلد فرعي (مثل Laragon)
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('\\', '/', dirname($scriptName));
        
        // إذا كان المجلد العام (public) جزءاً من المسار، نقوم بإزالته من المسار الأساسي
        if (str_ends_with($basePath, '/public')) {
            $basePath = substr($basePath, 0, -7);
        }

        // إزالة الجزء الخاص بالمجلدات من بداية الـ URI
        if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }

        // إزالة / من البداية والنهاية لتوحيد الشكل
        $requestUri = '/' . trim($requestUri, '/');
        
       
        // البحث عن المسار
        if (isset($this->routes[$requestMethod][$requestUri])) {
            
            $route = $this->routes[$requestMethod][$requestUri];
            $controllerName = $route['controller'];
            $methodName = $route['method'];
            

            
            // إنشاء كائن Controller واستدعاء Method
            $controller = new $controllerName();
            $controller->$methodName();
        } else {
            // صفحة 404
            http_response_code(404);
            echo "404 - الصفحة غير موجودة";
        }
        
    }
}
