Gate::define('isAdmin', function ($user) {
    return $user->role === 'admin';
});
