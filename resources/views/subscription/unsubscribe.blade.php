<x-main-layout title="Abonnement beenden" css="board" :no-breadcrumbs="true">
    <p>Möchtest Du das Abonnement des Themas »{{ $subscription->thread?->name }}« beenden?</p>
    <form method="post" action="{{ URL::signedRoute('subscription.unsubscribe', ['subscription' => $subscription->id]) }}">
        @csrf
        <button type="submit" class="fa fa-bell-slash"> Abonnement beenden</button>
    </form>
</x-main-layout>
