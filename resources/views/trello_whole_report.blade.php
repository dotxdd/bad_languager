<x-app-layout>
    <div class="container mx-auto mt-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Trello Pełny raport</h2>

        <!-- Wykres raportów -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h3 class="text-2xl font-semibold mb-4 text-blue-600">Zgłoszenia przez ostatnie 3 miesiące</h3>
            <canvas id="reportsChart"></canvas>
        </div>

        <!-- Filtr użytkowników -->
        <div class="mb-6">
            <label for="user-filter" class="block text-lg font-semibold text-gray-700">Filtruj po użytkownikach:</label>
            <select id="user-filter" class="p-2 border border-gray-300 rounded-md shadow-sm w-full bg-white">
                <option value="all">Wszyscy</option>
                <!-- Użytkownicy Trello zostaną załadowani tutaj dynamicznie -->
            </select>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <!-- Tabela dla użytkowników -->
            <h3 class="text-2xl font-semibold mb-4 text-blue-600">Raport użytkownikówt</h3>
            <div class="overflow-x-auto">
                <table id="users-full-table" class="min-w-full divide-y divide-gray-200 border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Reports</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div id="users-full-pagination" class="flex justify-end mt-4"></div>

            <!-- Tabela dla zadań -->
            <h3 class="text-2xl font-semibold mt-8 mb-4 text-blue-600">Raport kart</h3>
            <div class="overflow-x-auto">
                <table id="tasks-full-table" class="min-w-full divide-y divide-gray-200 border-collapse">
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
            <div id="tasks-full-pagination" class="flex justify-end mt-4"></div>

            <!-- Tabela dla komentarzy -->
            <h3 class="text-2xl font-semibold mt-8 mb-4 text-blue-600">Raport komentarzy</h3>
            <div class="overflow-x-auto">
                <table id="comments-full-table" class="min-w-full divide-y divide-gray-200 border-collapse">
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
            <div id="comments-full-pagination" class="flex justify-end mt-4"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const userFilter = document.getElementById('user-filter');
            const ctx = document.getElementById('reportsChart').getContext('2d');
            let reportsChart;

            // Funkcja do generowania dat dla ostatnich 3 miesięcy
            function getLastThreeMonthsDates() {
                const dates = [];
                const today = new Date();
                for (let i = 0; i < 90; i++) {
                    const date = new Date(today);
                    date.setDate(today.getDate() - i);
                    dates.push(date.toISOString().split('T')[0]); // Dodaj tylko datę
                }
                return dates.reverse(); // Odwróć, aby mieć od najstarszej do najnowszej
            }

            // Funkcja do ładowania danych wykresu
            function loadChartData() {
                fetch('{{ route('trello.get.chart') }}')
                    .then(response => response.json())
                    .then(data => {
                        const dates = getLastThreeMonthsDates();
                        const reportCounts = new Array(dates.length).fill(0); // Inicjalizuj zera

                        // Przypisz wartości raportów do odpowiednich dat
                        data.forEach(item => {
                            const reportDate = item.created_at.split('T')[0];
                            const index = dates.indexOf(reportDate);
                            if (index !== -1) {
                                reportCounts[index] = Math.floor(item.reports); // Ustaw liczbę raportów jako liczbę całkowitą
                            }
                        });

                        if (reportsChart) {
                            reportsChart.destroy();
                        }

                        reportsChart = new Chart(ctx, {
                            type: 'bar', // Użyj wykresu słupkowego
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
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            }

            // Pobieranie użytkowników z Trello i wstawianie do selektora
            fetch('{{ route('trello.get.members') }}')
                .then(response => response.json())
                .then(users => {
                    users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.text = user.name;
                        userFilter.appendChild(option);
                    });
                });

            const endpoints = [
                {
                    url: '{{ route('trello.toxic.whole.users') }}',
                    tableId: 'users-full-table',
                    paginationId: 'users-full-pagination'
                },
                {
                    url: '{{ route('trello.toxic.whole.tasks') }}',
                    tableId: 'tasks-full-table',
                    paginationId: 'tasks-full-pagination'
                },
                {
                    url: '{{ route('trello.toxic.whole.comments') }}',
                    tableId: 'comments-full-table',
                    paginationId: 'comments-full-pagination'
                }
            ];

            function updateReports(userIds) {
                endpoints.forEach(endpoint => {
                    loadData(endpoint.url, endpoint.tableId, endpoint.paginationId, 1, userIds);
                });
            }

            function loadData(url, tableId, paginationId, page, users) {
                let query = `${url}?page=${page}`;
                if (users && users.length > 0) {
                    query += `&users=${users.join(',')}`;  // Dodajemy ID użytkowników
                }

                fetch(query)
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.querySelector(`#${tableId} tbody`);
                        const paginationDiv = document.querySelector(`#${paginationId}`);
                        tableBody.innerHTML = '';

                        data.data.forEach(item => {
                            if (tableId === 'users-full-table') {
                                tableBody.innerHTML += `
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.email}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${Math.floor(item.total_report_count)}</td>
                                    </tr>`;
                            } else if (tableId === 'tasks-full-table') {
                                tableBody.innerHTML += `
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.card_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.card_description}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.board_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600"><a href="${item.url}" target="_blank">Link</a></td>
                                    </tr>`;
                            } else if (tableId === 'comments-full-table') {
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

                        setupPagination(paginationDiv, data.links, url, tableId, paginationId, users);
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            function setupPagination(paginationDiv, links, url, tableId, paginationId, users) {
                paginationDiv.innerHTML = '';
                links.forEach(link => {
                    const pageButton = document.createElement('button');
                    pageButton.innerText = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
                    pageButton.className = `mx-1 px-3 py-1 border rounded-lg ${link.active ? 'bg-blue-500 text-white' : 'bg-white text-gray-700'}`;
                    pageButton.addEventListener('click', () => {
                        if (!link.active && link.url) {
                            const page = new URL(link.url).searchParams.get('page');
                            loadData(url, tableId, paginationId, page, users);
                        }
                    });
                    paginationDiv.appendChild(pageButton);
                });
            }

            // Obsługa zmiany filtra
            userFilter.addEventListener('change', function () {
                const selectedUser = this.value;
                const userIds = selectedUser === 'all' ? [] : [selectedUser];
                updateReports(userIds);
            });

            // Wstępne załadowanie raportów dla "All Users"
            updateReports([]);
            loadChartData(); // Ładowanie danych do wykresu
        });
    </script>
</x-app-layout>
