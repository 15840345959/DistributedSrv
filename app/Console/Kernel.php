<?php

namespace App\Console;

use App\Components\ArticleManager;
use App\Components\Mryh\MryhCertSendManager;
use App\Components\Mryh\MryhComputePrizeManager;
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Utils;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteCertSendManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        ////////////////////////////////////////////////////////////////////////////
        ///  投票相关的计划任务
        ///
        /// By TerryQi
        ///
        //设置活动状态
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/vote_schedule.log'));
            Utils::processLog(__METHOD__, '', "activity status schedule start at:" . time());
            VoteActivityManager::activitySchedule();
            Utils::processLog(__METHOD__, '', "activity status schedule end at:" . time());
        })->everyFiveMinutes();

        //活动是否激活的提醒
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/vote_schedule.log'));
            Utils::processLog(__METHOD__, '', "activity valid schedule start at:" . time());
            VoteActivityManager::validActivitySchedule();
            Utils::processLog(__METHOD__, '', "activity valid schedule end at:" . time());
        })->dailyAt('9:00');

        //选手审核提醒
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/vote_schedule.log'));
            Utils::processLog(__METHOD__, '', "audit vote user schedule start at:" . time());
            VoteActivityManager::auditVoteUserSchedule();
            Utils::processLog(__METHOD__, '', "audit vote user schedule end at:" . time());
        })->dailyAt('17:00');

        //设置选手排名信息
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/vote_schedule.log'));
            Utils::processLog(__METHOD__, '', "activity vote user paiming schedule start at:" . time());
            VoteActivityManager::activityVoteUserPMSchedule();
            Utils::processLog(__METHOD__, '', "activity vote user paiming schedule end at:" . time());
        })->dailyAt('23:55');


        //每分钟处理发送证书任务
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/vote_schedule.log'));
            Utils::processLog(__METHOD__, '', "发送证书计划任务 start at:" . time());
            VoteCertSendManager::voteCertSendSchedule();
            Utils::processLog(__METHOD__, '', "发送证书计划任务 end at:" . time());
        })->everyMinute();

        //2018-09-13 运营组提出活动投票结束时间的条件控制
        //投票倒计时时间从激活时间当天开始算一共15天，第15天晚上10:10分结束
        //在未激活之前，显示报名倒计时，倒计时为15天，当达到激活条件后，自动变成投票倒计时15天
        //如果报名倒计时15天结束后仍未激活则显示投票倒计时15天
        //激活标准为大赛点击启动后个人页面被转发的量超过80%
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/vote_schedule.log'));
            Utils::processLog(__METHOD__, '', "设置活动投票结束时间 start at:" . time());
            VoteActivityManager::setVoteEndTimeSchedule();
            Utils::processLog(__METHOD__, '', "设置活动投票结束时间 end at:" . time());
        })->everyFiveMinutes();

        //2018-09-27 增加逻辑，每分钟执行，活动报名已经结束、投票未结束，设置激活状态，二次设置激活状态，例如活动在投票中，其激活状态的变化
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/vote_schedule.log'));
            Utils::processLog(__METHOD__, '', "设置报名结束且投票中的活动的激活状态 start at:" . time());
            VoteActivityManager::setVoteValidWhenApplyStatus2VoteStatus12();
            Utils::processLog(__METHOD__, '', "设置报名结束且投票中的活动的激活状态 end at:" . time());
        })->everyFiveMinutes();


        //2018-10-11 增加逻辑，每两小时执行一次，随机增加选手票数
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/vote_schedule.log'));
            Utils::processLog(__METHOD__, '', "每两小时执行一次，随机增加选手票数 start at:" . time());
            VoteActivityManager::addVoteUserVoteNumSchedule();
            Utils::processLog(__METHOD__, '', "每两小时执行一次，随机增加选手票数 end at:" . time());
        })->cron('30 */2 * * *');


        /////////////////////////////////////////////////////////////////////////////////////
        /// 每天一画相关计划任务
        ///
        /// By TerryQi
        ///
        ///
        //设置活动启停
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/mryh_schedule.log'));
            Utils::processLog(__METHOD__, '', "game status schedule start at:" . time());
            MryhGameManager::gameSchedule();
            Utils::processLog(__METHOD__, '', "game status schedule end at:" . time());
        })->everyMinute();

        //每天凌晨00:05执行，看看哪些用户失败了
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/mryh_schedule.log'));
            Utils::processLog(__METHOD__, '', "game user falied schedule start at:" . time());
            MryhJoinManager::joinSchedule();
            Utils::processLog(__METHOD__, '', "user falied schedule end at:" . time());
        })->dailyAt('00:05');

        //每天早上8:00执行清分任务
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/mryh_compute_prize_schedule.log'));
            Utils::processLog(__METHOD__, '', "game compute prize schedule start at:" . time());
            MryhGameManager::computePrizeSchedule();
            Utils::processLog(__METHOD__, '', "game compute prize schedule end at:" . time());
        })->dailyAt('08:00');

        //每天晚上进行提醒
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/mryh_schedule.log'));
            Utils::processLog(__METHOD__, '', "join notify schedule start at:" . time());
            MryhJoinManager::sendJoinNotifySchedule();
            Utils::processLog(__METHOD__, '', "join notify schedule end at:" . time());
        })->dailyAt('18:00');

        //每分钟处理发送证书任务
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/vote_schedule.log'));
            Utils::processLog(__METHOD__, '', "发送证书计划任务 start at:" . time());
            MryhCertSendManager::mryhCertSendSchedule();
            Utils::processLog(__METHOD__, '', "发送证书计划任务 end at:" . time());
        })->everyMinute();


        /////////////////////////////////////////////////////////////////////////////////////
        /// 艺术榜计划任务
        ///
        /// By TerryQi
        ///
        ///
        //进行作品自动审核
        $schedule->call(function () {
            Log::useFiles(storage_path('logs/ysb_schedule.log'));
            Utils::processLog(__METHOD__, '', "艺术榜进行作品自动审核 start at:" . time());
            ArticleManager::auditSchedule(Utils::XCX_ACCOUNT_CONFIG_VAL['ysb']);
            Utils::processLog(__METHOD__, '', "艺术榜进行作品自动审核 end at:" . time());
        })->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
