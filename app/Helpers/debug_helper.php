<?php

/**
 * Debug Helper
 * 
 * Verbesserte Logging- und Debug-Funktionen für SchulAG v2
 */

if (!function_exists('debug_log')) {
    /**
     * Debug-Log mit Context
     * 
     * @param string $message
     * @param array<string, mixed> $context
     * @param string $level
     * @return void
     */
    function debug_log(string $message, array $context = [], string $level = 'debug'): void
    {
        $levelMap = [
            'debug' => 'debug',
            'info' => 'info',
            'warning' => 'warning',
            'error' => 'error',
            'critical' => 'critical',
        ];
        
        $logLevel = $levelMap[$level] ?? 'debug';
        
        // Context formatieren
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        
        log_message($logLevel, $message . $contextStr);
    }
}

if (!function_exists('debug_user_action')) {
    /**
     * Log User-Aktionen
     * 
     * @param string $action
     * @param array<string, mixed> $data
     * @return void
     */
    function debug_user_action(string $action, array $data = []): void
    {
        $session = session();
        $userId = $session->get('user_id') ?? 'guest';
        $userName = $session->get('user_name') ?? 'Guest';
        $userRole = $session->get('user_role') ?? 'none';
        
        $context = [
            'user_id' => $userId,
            'user_name' => $userName,
            'user_role' => $userRole,
            'ip' => service('request')->getIPAddress(),
            'user_agent' => service('request')->getUserAgent()->getAgentString(),
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        
        if (!empty($data)) {
            $context['data'] = $data;
        }
        
        debug_log("User Action: {$action}", $context, 'info');
    }
}

if (!function_exists('debug_database_query')) {
    /**
     * Log Datenbank-Queries (nur in Development)
     * 
     * @param string $query
     * @param array<mixed> $binds
     * @param float $executionTime
     * @return void
     */
    function debug_database_query(string $query, array $binds = [], float $executionTime = 0.0): void
    {
        if (ENVIRONMENT !== 'development') {
            return;
        }
        
        $context = [
            'query' => $query,
            'binds' => $binds,
            'execution_time' => $executionTime . 's',
        ];
        
        debug_log('Database Query', $context, 'debug');
    }
}

if (!function_exists('debug_api_request')) {
    /**
     * Log API-Requests
     * 
     * @param string $endpoint
     * @param string $method
     * @param array<string, mixed> $data
     * @return void
     */
    function debug_api_request(string $endpoint, string $method, array $data = []): void
    {
        $context = [
            'endpoint' => $endpoint,
            'method' => $method,
            'data' => $data,
            'ip' => service('request')->getIPAddress(),
        ];
        
        debug_log("API Request: {$method} {$endpoint}", $context, 'info');
    }
}

if (!function_exists('debug_performance')) {
    /**
     * Log Performance-Metriken
     * 
     * @param string $action
     * @param float $startTime
     * @return void
     */
    function debug_performance(string $action, float $startTime): void
    {
        $executionTime = microtime(true) - $startTime;
        $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
        
        $context = [
            'action' => $action,
            'execution_time' => round($executionTime, 4) . 's',
            'memory_usage' => round($memoryUsage, 2) . 'MB',
        ];
        
        debug_log("Performance: {$action}", $context, 'debug');
    }
}

if (!function_exists('debug_allocation')) {
    /**
     * Log Losverfahren-Aktionen
     * 
     * @param string $step
     * @param array<string, mixed> $data
     * @return void
     */
    function debug_allocation(string $step, array $data = []): void
    {
        $context = [
            'step' => $step,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        
        if (!empty($data)) {
            $context = array_merge($context, $data);
        }
        
        debug_log("Allocation: {$step}", $context, 'info');
    }
}

if (!function_exists('debug_error')) {
    /**
     * Log Fehler mit Stack Trace
     * 
     * @param \Throwable $exception
     * @param array<string, mixed> $additionalContext
     * @return void
     */
    function debug_error(\Throwable $exception, array $additionalContext = []): void
    {
        $context = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ];
        
        if (!empty($additionalContext)) {
            $context['additional'] = $additionalContext;
        }
        
        debug_log('Exception caught', $context, 'error');
    }
}

if (!function_exists('debug_security')) {
    /**
     * Log Security-Events
     * 
     * @param string $event
     * @param array<string, mixed> $data
     * @return void
     */
    function debug_security(string $event, array $data = []): void
    {
        $context = [
            'event' => $event,
            'ip' => service('request')->getIPAddress(),
            'user_agent' => service('request')->getUserAgent()->getAgentString(),
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        
        if (!empty($data)) {
            $context['data'] = $data;
        }
        
        debug_log("Security: {$event}", $context, 'warning');
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and Die (für Debugging)
     * 
     * @param mixed ...$vars
     * @return never
     */
    function dd(...$vars): never
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
        die(1);
    }
}

if (!function_exists('dump_query')) {
    /**
     * Dump letzten Query aus DB
     * 
     * @return void
     */
    function dump_query(): void
    {
        if (ENVIRONMENT !== 'development') {
            return;
        }
        
        $db = \Config\Database::connect();
        echo '<pre>';
        echo $db->getLastQuery();
        echo '</pre>';
    }
}


