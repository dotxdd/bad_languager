<x-app-layout>
    <div class="container mx-auto mt-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Trello raport miesięczny</h2>

        <!-- Wybór użytkownika -->
        <div class="mb-6">
            <label for="user-picker" class="block text-lg font-semibold text-gray-700">Wybierz użytkownika:</label>
            <select id="user-picker" class="p-2 border border-gray-300 rounded-md shadow-sm w-full bg-white">
                <!-- Opcje zostaną dodane dynamicznie -->
            </select>
        </div>

        <!-- Wybór miesiąca -->
        <div class="mb-6">
            <label for="date-picker" class="block text-lg font-semibold text-gray-700">Wybierz miesiąc u rok:</label>
            <select id="date-picker" class="p-2 border border-gray-300 rounded-md shadow-sm w-full bg-white">
                <!-- Opcje zostaną dodane dynamicznie -->
            </select>
        </div>

        <!-- Wykres -->
        <div class="mb-6">
            <canvas id="reportsChart" class="border border-gray-300"></canvas>
        </div>

        <!-- Raporty -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-2xl font-semibold mb-4 text-blue-600">Raport użytkowników</h3>
            <div class="overflow-x-auto">
                <table id="users-table" class="min-w-full divide-y divide-gray-200 border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Reports</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div id="users-pagination" class="flex justify-end mt-4"></div>

            <h3 class="text-2xl font-semibold mt-8 mb-4 text-blue-600">Raport zadań</h3>
            <div class="overflow-x-auto">
                <table id="tasks-table" class="min-w-full divide-y divide-gray-200 border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Board</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div id="tasks-pagination" class="flex justify-end mt-4"></div>

            <h3 class="text-2xl font-semibold mt-8 mb-4 text-blue-600">Raport komentarzy</h3>
            <div class="overflow-x-auto">
                <table id="comments-table" class="min-w-full divide-y divide-gray-200 border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Board</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div id="comments-pagination" class="flex justify-end mt-4"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const datePicker = document.getElementById('date-picker');
            const userPicker = document.getElementById('user-picker');
            const ctx = document.getElementById('reportsChart').getContext('2d');
            let reportsChart;

            const currentYear = new Date().getFullYear();
            const currentMonth = new Date().getMonth() + 1;

            // Generowanie opcji dla wyboru daty
            function generateDateOptions() {
                let options = '';
                for (let year = currentYear - 10; year <= currentYear; year++) {
                    for (let month = 1; month <= 12; month++) {
                        const formattedMonth = String(month).padStart(2, '0');
                        const formattedDate = `${year}-${formattedMonth}`;
                        const monthName = new Date(year, month - 1).toLocaleString('default', { month: 'long' });
                        options += `<option value="${formattedDate}">${monthName} ${year}</option>`;
                    }
                }
                datePicker.innerHTML = options;
            }

            // Pobieranie użytkowników
            function loadUsers() {
                fetch('{{ route('trello.get.members') }}')
                    .then(response => response.json())
                    .then(data => {
                        let options = '<option value="">Wszyscy</option>';
                        data.forEach(user => {
                            options += `<option value="${user.id}">${user.name}</option>`;
                        });
                        userPicker.innerHTML = options;
                    });
            }

            generateDateOptions();
            loadUsers();
            datePicker.value = `${currentYear}-${String(currentMonth).padStart(2, '0')}`;

            // Endpointy do ładowania danych
            const endpoints = [
                {
                    url: '{{ route('trello.toxic.whole.monthly') }}',
                    tableId: 'users-table',
                    paginationId: 'users-pagination'
                },
                {
                    url: '{{ route('trello.toxic.whole.tasks.monthly') }}',
                    tableId: 'tasks-table',
                    paginationId: 'tasks-pagination'
                },
                {
                    url: '{{ route('trello.toxic.whole.comments.monthly') }}',
                    tableId: 'comments-table',
                    paginationId: 'comments-pagination'
                }
            ];

            // Aktualizacja raportów i wykresu
            function updateReports(date, userId) {
                loadChartData(date);
                endpoints.forEach(endpoint => {
                    loadData(endpoint.url, endpoint.tableId, endpoint.paginationId, 1, date, userId);
                });
            }

            function loadChartData(date) {
                fetch('{{ route('trello.get.chart') }}?month=' + date)
                    .then(response => response.json())
                    .then(data => {
                        const dates = Array.from({ length: new Date(currentYear, currentMonth, 0).getDate() }, (_, i) =>
                            new Date(currentYear, currentMonth - 1, i + 1).toISOString().split('T')[0]
                        );

                        const reportCounts = new Array(dates.length).fill(0);

                        data.forEach(item => {
                            const reportDate = item.created_at.split('T')[0];
                            const index = dates.indexOf(reportDate);
                            if (index !== -1) {
                                reportCounts[index] = Math.floor(item.reports);
                            }
                        });

                        if (reportsChart) {
                            reportsChart.destroy();
                        }

                        reportsChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: dates,
                                datasets: [{
                                    label: 'Zgłoszeń dziennie',
                                    data: reportCounts,
                                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Liczba zgłoszeń'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Data'
                                        }
                                    }
                                }
                            }
                        });
                    });
            }

            function loadData(url, tableId, paginationId, page, date, userId) {
                let queryUrl = `${url}?page=${page}&month=${date}`;
                if (userId) {
                    queryUrl += `&users[]=${userId}`;
                }

                fetch(queryUrl)
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.querySelector(`#${tableId} tbody`);
                        const paginationDiv = document.querySelector(`#${paginationId}`);
                        tableBody.innerHTML = '';

                        data.data.forEach(item => {
                            if (tableId === 'users-table') {
                                tableBody.innerHTML += `
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.email}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.total_report_count}</td>
                                    </tr>`;
                            } else if (tableId === 'tasks-table') {
                                tableBody.innerHTML += `
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.card_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.card_description}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.board_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600"><a href="${item.url}" target="_blank">Link</a></td>
                                    </tr>`;
                            } else if (tableId === 'comments-table') {
                                tableBody.innerHTML += `
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.comment}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.card_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.board_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600"><a href="${item.url}" target="_blank">Link</a></td>
                                    </tr>`;
                            }
                        });

                        const pagination = data.links.map(link => `
                            <a href="#" data-page="${link.label}" class="px-4 py-2 mx-1 border rounded ${link.active ? 'bg-blue-500 text-white' : 'text-blue-500'}">${link.label}</a>
                        `).join('');
                        paginationDiv.innerHTML = pagination;

                        paginationDiv.querySelectorAll('a').forEach(link => {
                            link.addEventListener('click', function (e) {
                                e.preventDefault();
                                const page = this.dataset.page;
                                loadData(url, tableId, paginationId, page, date, userId);
                            });
                        });
                    });
            }

            updateReports(datePicker.value, '');

            datePicker.addEventListener('change', () => {
                updateReports(datePicker.value, userPicker.value);
            });

            userPicker.addEventListener('change', () => {
                updateReports(datePicker.value, userPicker.value);
            });
        });
    </script>
</x-app-layout>
