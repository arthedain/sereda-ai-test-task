<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="p-6 text-sm font-bold mb-2 text-gray-900 dark:text-gray-100">
                        <p>Тариф: {{ $subscription_name ?? '' }}</p>
                        <p>Кількість користувачів: {{ $team_members_count ?? '' }}</p>
                        <p>Загальна вартість: {{ $subscription_amount ?? '' }}</p>
                        <p>Періодичність оплати: {{ $subscription_interval ?? '' }}</p>
                        <p>Діє до (наступна оплата): {{ $subscription_next_payment_date ?? '' }}</p>
                    </div>

                    @if(isset($has_pending_subscription) && $has_pending_subscription)
                        <div class="p-6 text-sm font-bold mb-2 text-gray-900 dark:text-gray-100">
                            <h2>Наступна підписка</h2>
                            <p>Тариф: {{ $pending_subscription_name ?? '' }}</p>
                            <p>Кількість користувачів: {{ $pending_team_members_count ?? '' }}</p>
                            <p>Загальна вартість: {{ $pending_subscription_amount ?? '' }}</p>
                            <p>Періодичність оплати: {{ $pending_subscription_interval ?? '' }}</p>
                            <p>Активна з: {{ $pending_subscription_start_date ?? '' }}</p>
                        </div>
                    @endif

                    <div class="flex justify-center items-center">
                        <form method="POST" action="{{ route('subscription.change-plan', ['team' => $team_id]) }}" class="p-6 w-full max-w-md">
                            @csrf
                            <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Змінити підписку</h2>
                            <div class="mb-4">
                                <label for="plan_id" class="block text-sm font-medium mb-2 text-gray-900 dark:text-gray-100">Підписка</label>
                                <select id="plan_id" name="plan_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-300">
                                    <option value="" disabled selected>Виберіть підписку</option>
                                    @foreach ($subscription_plans as $plan)
                                        <option {{ $plan->name === $subscription_name ? 'disabled' : '' }} value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="w-full bg-blue-500 dark:bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-600 dark:hover:bg-blue-700 transition-colors duration-200">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
