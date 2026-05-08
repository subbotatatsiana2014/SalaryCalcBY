<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;

class SettingsController extends Controller
{
    protected $envPath;

    public function __construct()
    {
        $this->envPath = base_path('.env');
    }

    public function index()
    {
        $settings = Cache::remember('company_settings', 86400, function () {
            return $this->loadEnvSettings();
        });

        return view('settings.index', compact('settings'));
    }

    /**
     * Загрузка настроек из .env файла
     */
    protected function loadEnvSettings()
    {
        $envContent = File::get($this->envPath);
        $settings = [];

        $lines = explode("\n", $envContent);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $settings[$key] = $value;
            }
        }

        return $settings;
    }

    /**
     * Сохранение значения в .env файл
     */
    protected function setEnvValue($key, $value)
    {
        $envContent = File::get($this->envPath);
        $pattern = "/^{$key}=.*/m";
        $value = $this->escapeEnvValue($value);

        if (preg_match($pattern, $envContent)) {
            $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
        } else {
            $envContent .= "\n{$key}={$value}";
        }

        File::put($this->envPath, $envContent);
    }

    /**
     * Экранирование значения для .env файла
     */
    protected function escapeEnvValue($value)
    {
        if (preg_match('/\s|#|\'|"/', $value)) {
            $value = '"' . addslashes($value) . '"';
        }

        return $value;
    }

    /**
     * Сохранение основных настроек
     */
    public function saveGeneral(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'app_timezone' => 'required|string',
            'app_locale' => 'required|string|in:ru,en,be',
        ]);

        try {
            $this->setEnvValue('APP_NAME', $validated['app_name']);
            $this->setEnvValue('APP_URL', $validated['app_url']);
            $this->setEnvValue('APP_TIMEZONE', $validated['app_timezone']);
            $this->setEnvValue('APP_LOCALE', $validated['app_locale']);

            Artisan::call('config:clear');
            Artisan::call('config:cache');
            CacheService::flushSettings();
            CacheService::flushDepartments();

            return redirect()->route('settings.index')
                ->with('success', 'Основные настройки успешно сохранены!');
        } catch (\Exception $e) {
            \Log::error('Error saving general settings: ' . $e->getMessage());
            return redirect()->route('settings.index')
                ->with('error', 'Ошибка при сохранении основных настроек: ' . $e->getMessage());
        }
    }

    /**
     * Сохранение настроек компании
     */
    public function saveCompany(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email',
            'company_phone' => 'nullable|string',
            'company_address' => 'nullable|string',
            'company_tax_id' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $this->setEnvValue('COMPANY_NAME', $validated['company_name']);
            $this->setEnvValue('COMPANY_EMAIL', $validated['company_email']);
            $this->setEnvValue('COMPANY_PHONE', $validated['company_phone'] ?? '');
            $this->setEnvValue('COMPANY_ADDRESS', $validated['company_address'] ?? '');
            $this->setEnvValue('COMPANY_TAX_ID', $validated['company_tax_id'] ?? '');

            if ($request->hasFile('company_logo')) {
                $logo = $request->file('company_logo');
                $logoName = 'logo.' . $logo->getClientOriginalExtension();
                $logoPath = $logo->storeAs('company', $logoName, 'public');
                $this->setEnvValue('COMPANY_LOGO', '/storage/' . $logoPath);
            }

            Artisan::call('config:clear');
            Artisan::call('config:cache');

            return redirect()->route('settings.index')
                ->with('success', 'Настройки компании успешно сохранены!');
        } catch (\Exception $e) {
            \Log::error('Error saving company settings: ' . $e->getMessage());
            return redirect()->route('settings.index')
                ->with('error', 'Ошибка при сохранении настроек компании: ' . $e->getMessage());
        }
    }

    /**
     * Сохранение настроек уведомлений
     */
    public function saveNotifications(Request $request)
    {
        $validated = $request->validate([
            'notify_new_employee' => 'nullable|boolean',
            'notify_salary_calc' => 'nullable|boolean',
            'notify_tax_deadline' => 'nullable|boolean',
            'notify_contract_expiry' => 'nullable|boolean',
            'notification_email' => 'required|email',
        ]);

        try {
            $this->setEnvValue('NOTIFY_NEW_EMPLOYEE', $validated['notify_new_employee'] ? 'true' : 'false');
            $this->setEnvValue('NOTIFY_SALARY_CALC', $validated['notify_salary_calc'] ? 'true' : 'false');
            $this->setEnvValue('NOTIFY_TAX_DEADLINE', $validated['notify_tax_deadline'] ? 'true' : 'false');
            $this->setEnvValue('NOTIFY_CONTRACT_EXPIRY', $validated['notify_contract_expiry'] ? 'true' : 'false');
            $this->setEnvValue('NOTIFICATION_EMAIL', $validated['notification_email']);

            Artisan::call('config:clear');
            Artisan::call('config:cache');

            return redirect()->route('settings.index')
                ->with('success', 'Настройки уведомлений успешно сохранены!');
        } catch (\Exception $e) {
            \Log::error('Error saving notification settings: ' . $e->getMessage());
            return redirect()->route('settings.index')
                ->with('error', 'Ошибка при сохранении настроек уведомлений: ' . $e->getMessage());
        }
    }

    /**
     * Сохранение настроек безопасности
     */
    public function saveSecurity(Request $request)
    {
        $validated = $request->validate([
            'session_lifetime' => 'required|integer|min:1|max:1440',
            'session_encrypt' => 'nullable|boolean',
            'password_min_length' => 'required|integer|min:6|max:20',
            'require_strong_password' => 'nullable|boolean',
        ]);

        try {
            $this->setEnvValue('SESSION_LIFETIME', $validated['session_lifetime']);
            $this->setEnvValue('SESSION_ENCRYPT', $validated['session_encrypt'] ? 'true' : 'false');
            $this->setEnvValue('PASSWORD_MIN_LENGTH', $validated['password_min_length']);
            $this->setEnvValue('REQUIRE_STRONG_PASSWORD', $validated['require_strong_password'] ? 'true' : 'false');

            Artisan::call('config:clear');
            Artisan::call('config:cache');

            return redirect()->route('settings.index')
                ->with('success', 'Настройки безопасности успешно сохранены!');
        } catch (\Exception $e) {
            \Log::error('Error saving security settings: ' . $e->getMessage());
            return redirect()->route('settings.index')
                ->with('error', 'Ошибка при сохранении настроек безопасности: ' . $e->getMessage());
        }
    }

    public function deleteLogo(Request $request)
    {
        try {
            $currentLogo = env('COMPANY_LOGO', '');
            $currentLogo = trim($currentLogo, ' "\'');

            if (!empty($currentLogo) && $currentLogo !== '""' && $currentLogo !== 'null') {
                $path = str_replace('/storage/', '', $currentLogo);
                $fullPath = storage_path('app/public/' . $path);

                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $this->setEnvValue('COMPANY_LOGO', '');
            Artisan::call('config:clear');
            Artisan::call('config:cache');

            return response()->json(['success' => true, 'message' => 'Логотип удален']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
