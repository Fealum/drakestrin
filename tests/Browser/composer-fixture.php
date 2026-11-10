<?php

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\Board\ThreadScene;
use App\Models\Economy\Inventory;
use App\Models\Territory\Location;
use App\Models\Territory\Territory;
use App\Models\User;
use App\Models\User\Character;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\CreatesApplication;

require __DIR__.'/../../vendor/autoload.php';
$bootstrap = new class
{
    use CreatesApplication;
};
$bootstrap->createApplication();
$path = storage_path('app/testing/composer-browser.json');

if (($argv[1] ?? '') === 'cleanup') {
    if (File::exists($path)) {
        $fixture = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);
        DB::table('thread_scenes')->where('thread_id', $fixture['thread'])->delete();
        DB::table('posts')->where('thread_id', $fixture['thread'])->delete();
        DB::table('threads')->where('id', $fixture['thread'])->delete();
        DB::table('boards')->where('id', $fixture['board'])->delete();
        DB::table('inventories')->where('owner_type', PermissionEntityType::CHARACTER->value)->whereIn('owner_id', $fixture['characters'])->delete();
        DB::table('locations')->where('id', $fixture['location'])->delete();
        DB::table('group_user')->where('user_id', $fixture['user'])->delete();
        DB::table('permissions')->where('recipient_type', PermissionEntityType::USER->value)->where('recipient_id', $fixture['user'])->delete();
        DB::table('onlines')->where('user_id', $fixture['user'])->delete();
        DB::table('characters')->where('user_id', $fixture['user'])->delete();
        DB::table('users')->where('id', $fixture['user'])->delete();
        File::delete($path);
    }
    exit;
}
if (File::exists($path)) {
    throw new RuntimeException('Clean up the previous browser fixture before creating another.');
}
$user = User::factory()->create(['name' => 'Composer Browser Test', 'email' => 'composer-browser@example.test', 'password' => Hash::make('browser-test-only')]);
DB::table('group_user')->insert(['user_id' => $user->id, 'group_id' => 2]);
$character = Character::factory()->create(['user_id' => $user->id, 'name' => 'Browser First']);
$other = Character::factory()->create(['user_id' => $user->id, 'name' => 'Browser Second']);
$board = Board::factory()->create(['name' => 'Composer Browser Test', 'parent_id' => 0]);
$thread = Thread::factory()->create(['board_id' => $board->id, 'name' => 'Composer Browser Test']);
foreach (DB::table('permits')->whereIn('name', ['setthreadscene', 'endthreadscene', 'createpoll', 'createcharacter'])->pluck('id') as $permitId) {
    DB::table('permissions')->insert([
        'recipient_type' => PermissionEntityType::USER->value, 'recipient_id' => $user->id,
        'subject_type' => PermissionEntityType::THREAD->value, 'subject_id' => $thread->id,
        'permit_id' => $permitId, 'value' => 2,
    ]);
}
$posts = collect();
$recipients = collect();
for ($index = 1; $index <= 6; $index++) {
    $recipient = Character::factory()->create(['user_id' => $user->id, 'name' => 'Recipient '.$index]);
    $recipients->push($recipient);
    $posts->push(Post::factory()->withMessage('Earlier conversation '.$index)->create([
        'thread_id' => $thread->id, 'board_id' => $board->id, 'user_id' => $user->id,
        'character_id' => $recipient->id, 'time' => now()->subDays(2),
    ]));
}
for ($index = 0; $index < 21; $index++) {
    $posts->push(Post::factory()->withMessage('Browser fixture '.$index)->create([
        'thread_id' => $thread->id, 'board_id' => $board->id, 'user_id' => $user->id,
        'character_id' => $character->id, 'time' => now()->subMinutes(21 - $index),
    ]));
}
$thread->update(['first_post_id' => $posts->first()->id, 'last_post_id' => $posts->last()->id, 'post_count' => $posts->count()]);
$location = Location::factory()->create([
    'name' => 'Composer Browser Test', 'parent_type' => PermissionEntityType::TERRITORY,
    'parent_id' => Territory::query()->whereDoesntHave('children')->value('id'),
]);
ThreadScene::create([
    'thread_id' => $thread->id, 'location_id' => $location->id,
    'starts_at_post_id' => $posts->first()->id, 'story_started_at' => now()->timestamp, 'created_by_user_id' => $user->id,
]);
Inventory::create(['item_id' => 1, 'stack' => 8192, 'wear' => 0, 'owner_type' => PermissionEntityType::CHARACTER, 'owner_id' => $character->id, 'timelastvalue' => 0, 'data' => '']);
File::ensureDirectoryExists(dirname($path));
File::put($path, json_encode([
    'user' => $user->id, 'thread' => $thread->id, 'board' => $board->id,
    'location' => $location->id, 'characters' => [$character->id, $other->id, ...$recipients->pluck('id')->all()],
], JSON_THROW_ON_ERROR));
echo "Browser fixture ready in the isolated testing database.\n";
