<?php

namespace Encore\Admin\Controllers;

use App\Paper;
use Encore\Admin\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Dashboard
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function title($username, $role)
    {
        $title = [
            // 管理员
            1 => [
                'username' => $username,
                'title'   => [
                    ['name' => '套卷管理', 'url'  => "/admin/paper"],
                    ['name' => '学生答案', 'url'  => "/admin/answer"],
                    ['name' => '反馈管理', 'url'  => "/admin/auth/feedback"],
                ],
            ],
            // 教师
            3 => [
                'username' => $username,
                'title'   => [
                    ['name' => '套卷管理', 'url'  => "/admin/paper"],
                    ['name' => '学生答案', 'url'  => "/admin/answer"],
                    ['name' => '我的反馈', 'url'  => "/admin/feedback"],
                ],
            ],
            // 学生
            2 => [
                'username' => $username,
                'title'   => [
                    ['name' => '我的试卷', 'url'  => "/admin/student/paper"],
                    ['name' => '我的反馈', 'url'  => "/admin/feedback"],
                ],
            ],
        ];
        return view('admin::dashboard.title')->with('title', $title[$role]);
    }

    /**
     * 展示试卷
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function papers()
    {
        $papers = DB::table('paper')->orderBy('id', 'desc')->take(5)->get(['id', 'classname', 'year'])->toArray();
        return view('admin::dashboard.papers', compact('papers'));
    }

    /**
     * 展示学生答案
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function answers()
    {
        $answers = DB::table('student_score')->join('paper', 'student_score.paper_id', '=', 'paper.id')->orderBy('student_score.id', 'desc')->take(5)->get(['student_score.id', 'student_score.username', 'paper.classname'])->toArray();
        return view('admin::dashboard.answers', compact('answers'));
    }

    /**
     * 展示用户反馈
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function feedbacks()
    {
        $feedbacks = DB::table('feedback')->join('admin_users', 'feedback.uid', '=', 'admin_users.id')->orderBy('feedback.id', 'desc')->take(5)->get(['feedback.status','admin_users.name'])->toArray();
        return view('admin::dashboard.feedbacks', compact('feedbacks'));
    }

    /**
     * 展示我的反馈
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function myfeedbacks()
    {
        $myfeedbacks = DB::table('feedback')->where('uid', \Encore\Admin\Facades\Admin::user()->id)->orderBy('created_at', 'desc')->take(5)->get(['status','content'])->toArray();
        return view('admin::dashboard.myfeedbacks', compact('myfeedbacks'));
    }

    /**
     * 展示我的试卷
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function mypapers()
    {
        $mypapers = DB::table('student_score')->where('username', \Encore\Admin\Facades\Admin::user()->username)->join('paper', 'student_score.paper_id', '=', 'paper.id')->orderBy('student_score.created_at', 'desc')->take(5)->get(['student_score.id', 'student_score.score','paper.classname'])->toArray();
        return view('admin::dashboard.mypapers', compact('mypapers'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function environment()
    {
        $envs = [
            ['name' => 'PHP version',       'value' => 'PHP/'.PHP_VERSION],
            ['name' => 'Laravel version',   'value' => app()->version()],
            ['name' => 'CGI',               'value' => php_sapi_name()],
            ['name' => 'Uname',             'value' => php_uname()],
            ['name' => 'Server',            'value' => Arr::get($_SERVER, 'SERVER_SOFTWARE')],

            ['name' => 'Cache driver',      'value' => config('cache.default')],
            ['name' => 'Session driver',    'value' => config('session.driver')],
            ['name' => 'Queue driver',      'value' => config('queue.default')],

            ['name' => 'Timezone',          'value' => config('app.timezone')],
            ['name' => 'Locale',            'value' => config('app.locale')],
            ['name' => 'Env',               'value' => config('app.env')],
            ['name' => 'URL',               'value' => config('app.url')],
        ];

        return view('admin::dashboard.environment', compact('envs'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function extensions()
    {
        $extensions = [
            'helpers' => [
                'name' => 'laravel-admin-ext/helpers',
                'link' => 'https://github.com/laravel-admin-extensions/helpers',
                'icon' => 'gears',
            ],
            'log-viewer' => [
                'name' => 'laravel-admin-ext/log-viewer',
                'link' => 'https://github.com/laravel-admin-extensions/log-viewer',
                'icon' => 'database',
            ],
            'backup' => [
                'name' => 'laravel-admin-ext/backup',
                'link' => 'https://github.com/laravel-admin-extensions/backup',
                'icon' => 'copy',
            ],
            'config' => [
                'name' => 'laravel-admin-ext/config',
                'link' => 'https://github.com/laravel-admin-extensions/config',
                'icon' => 'toggle-on',
            ],
            'api-tester' => [
                'name' => 'laravel-admin-ext/api-tester',
                'link' => 'https://github.com/laravel-admin-extensions/api-tester',
                'icon' => 'sliders',
            ],
            'media-manager' => [
                'name' => 'laravel-admin-ext/media-manager',
                'link' => 'https://github.com/laravel-admin-extensions/media-manager',
                'icon' => 'file',
            ],
            'scheduling' => [
                'name' => 'laravel-admin-ext/scheduling',
                'link' => 'https://github.com/laravel-admin-extensions/scheduling',
                'icon' => 'clock-o',
            ],
            'reporter' => [
                'name' => 'laravel-admin-ext/reporter',
                'link' => 'https://github.com/laravel-admin-extensions/reporter',
                'icon' => 'bug',
            ],
            'redis-manager' => [
                'name' => 'laravel-admin-ext/redis-manager',
                'link' => 'https://github.com/laravel-admin-extensions/redis-manager',
                'icon' => 'flask',
            ],
        ];

        foreach ($extensions as &$extension) {
            $name = explode('/', $extension['name']);
            $extension['installed'] = array_key_exists(end($name), Admin::$extensions);
        }

        return view('admin::dashboard.extensions', compact('extensions'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function dependencies()
    {
        $json = file_get_contents(base_path('composer.json'));

        $dependencies = json_decode($json, true)['require'];

        Admin::script("$('.dependencies').slimscroll({height:'510px',size:'3px'});");

        return view('admin::dashboard.dependencies', compact('dependencies'));
    }
}
