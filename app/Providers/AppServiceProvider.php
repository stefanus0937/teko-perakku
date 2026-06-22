<?php

namespace App\Providers;
use App\Http\Middleware\RoleCheck; // pastikan ada use ini di atas
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route; // pastikan ada use ini di atas
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::aliasMiddleware('role', RoleCheck::class);
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\View::composer(['guest.layouts.main', 'layouts.user', 'layouts.umkm', 'layouts.admin_premium'], function ($view) {
            $kategoris = \App\Models\KategoriProduk::ordered()->get();
            $structuredKategoris = $kategoris->where('sort_order', '>', 0);

            $view->with('kategoris', $kategoris);
            $view->with('categoryGroups', $structuredKategoris->groupBy('category_type'));
            $view->with('categoryTypeLabels', \App\Models\KategoriProduk::TYPE_LABELS);
            $view->with('randomKategoris', \App\Models\KategoriProduk::inRandomOrder()->get());
        });

        \Illuminate\Support\Facades\Event::listen(\JeroenNoten\LaravelAdminLte\Events\BuildingMenu::class, function (\JeroenNoten\LaravelAdminLte\Events\BuildingMenu $event) {
            $user = \Illuminate\Support\Facades\Auth::user();

            if (!$user) {
                return;
            }
            
            // ... rest of the code remains same

            // Dynamic User Menu in Top Navbar
            $event->menu->add([
                'text' => $user->username,
                'topnav_right' => true,
                'icon' => 'fas fa-user',
                'submenu' => [
                    [
                        'text' => __('navigation.profile'),
                        'url' => 'admin/profile',
                        'icon' => 'fas fa-id-badge',
                    ],
                    [
                        'text' => __('auth.change_password'),
                        'url' => 'admin/change-password',
                        'icon' => 'fas fa-key',
                    ],
                    [
                        'text' => __('navigation.logout'),
                        'url' => '#',
                        'icon' => 'fas fa-sign-out-alt',
                        'id' => 'logout-button',
                    ]
                ],
            ]);

            // Common items for many roles
            $role = $user->role;

            if ($role === 'admin_utama') {
                $event->menu->add([
                    'text' => __('navigation.profile'),
                    'url'  => 'admin/profile',
                    'icon' => 'fas fa-fw fa-user',
                ]);
                $event->menu->add([
                    'text' => __('navigation.manage_admin'),
                    'url'  => '#',
                    'icon' => 'fas fa-fw fa-users-cog',
                ]);
                $event->menu->add([
                    'text' => __('navigation.craftsmen'),
                    'url'  => 'admin/pengerajin',
                    'icon' => 'fas fa-fw fa-users',
                ]);
                $event->menu->add([
                    'text' => __('navigation.businesses'),
                    'url'  => 'admin/usaha',
                    'icon' => 'fas fa-fw fa-briefcase',
                ]);
                $event->menu->add([
                    'text' => __('navigation.products'),
                    'url'  => 'admin/produk',
                    'icon' => 'fas fa-fw fa-box',
                ]);
                $event->menu->add([
                    'text' => __('navigation.reports'),
                    'url'  => 'admin/export-data',
                    'icon' => 'fas fa-fw fa-file-export',
                ]);
            } elseif ($role === 'admin_wilayah') {
                $event->menu->add([
                    'text' => __('navigation.profile'),
                    'url'  => 'admin/profile',
                    'icon' => 'fas fa-fw fa-user',
                ]);
                $event->menu->add([
                    'text' => __('navigation.craftsmen'),
                    'url'  => 'admin/pengerajin',
                    'icon' => 'fas fa-fw fa-users',
                ]);
                $event->menu->add([
                    'text' => __('navigation.businesses'),
                    'url'  => 'admin/usaha',
                    'icon' => 'fas fa-fw fa-briefcase',
                ]);
                $event->menu->add([
                    'text' => __('navigation.products'),
                    'url'  => 'admin/produk',
                    'icon' => 'fas fa-fw fa-box',
                ]);
                $event->menu->add([
                    'text' => __('navigation.reports'),
                    'url'  => 'admin/export-data',
                    'icon' => 'fas fa-fw fa-file-export',
                ]);
            } elseif ($role === 'umkm') {
                $event->menu->add([
                    'text' => __('navigation.profile'),
                    'url'  => 'admin/profile',
                    'icon' => 'fas fa-fw fa-user',
                ]);
                $unreadCount = \App\Models\Chat::where('receiver_id', $user->id)->where('is_read', false)->count();
                $event->menu->add([
                    'text' => __('navigation.chat'),
                    'url'  => 'chats',
                    'icon' => 'fas fa-fw fa-comments',
                    'label' => $unreadCount > 0 ? $unreadCount : null,
                    'label_color' => 'primary',
                ]);
                $event->menu->add([
                    'text' => __('navigation.products'),
                    'url'  => 'admin/produk',
                    'icon' => 'fas fa-fw fa-box',
                ]);
                $event->menu->add([
                    'text' => __('navigation.settings'),
                    'url'  => '#',
                    'icon' => 'fas fa-fw fa-cogs',
                ]);
                $event->menu->add([
                    'text' => __('navigation.reports'),
                    'url'  => 'admin/export-data',
                    'icon' => 'fas fa-fw fa-file-export',
                ]);
            } elseif ($role === 'user') {
                $event->menu->add([
                    'text' => __('navigation.profile'),
                    'url'  => 'admin/profile',
                    'icon' => 'fas fa-fw fa-user',
                ]);
                $unreadCount = \App\Models\Chat::where('receiver_id', $user->id)->where('is_read', false)->count();
                $event->menu->add([
                    'text' => __('navigation.chat'),
                    'url'  => 'chats',
                    'icon' => 'fas fa-fw fa-comments',
                    'label' => $unreadCount > 0 ? $unreadCount : null,
                    'label_color' => 'primary',
                ]);
                $event->menu->add([
                    'text' => __('navigation.favorites'),
                    'url'  => '#',
                    'icon' => 'fas fa-fw fa-heart',
                ]);
                $event->menu->add([
                    'text' => __('navigation.settings'),
                    'url'  => '#',
                    'icon' => 'fas fa-fw fa-cogs',
                ]);
            }
        });
    }
}
