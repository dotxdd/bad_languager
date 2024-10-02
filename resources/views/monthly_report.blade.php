<x-app-layout>
    <div class="container mx-auto py-8">
        <h2 class="text-2xl font-bold mb-4">Monthly ClickUp Report</h2>

        <!-- User Filter -->
        <div class="mb-6">
            <label for="user-filter" class="block text-sm font-medium text-gray-700">Filter by Users:</label>
            <select id="user-filter" class="mt-1 block w-64 p-2 border border-gray-300 rounded-lg shadow-sm">
                <option value="all">Wszyscy</option>
            </select>
        </div>

        <div class="mb-6">
            <label for="month-picker" class="block text-sm font-medium text-gray-700">Select Month:</label>
            <input type="month" id="month-picker" class="mt-1 block w-64 p-2 border border-gray-300 rounded-lg shadow-sm">
        </div>

        <!-- Toxic Users Table -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-2">Toxic Users</h3>
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table id="users-table" class="min-w-full bg-white border-collapse table-auto rounded-lg">
                    <thead class="bg-gray-200 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold border-b">Name</th>
                        <th class="px-6 py-3 text-left font-semibold border-b">Email</th>
                        <th class="px-6 py-3 text-left font-semibold border-b">Reports</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
            <div id="users-pagination" class="mt-4"></div>
        </div>

        <!-- Chart Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-2">Monthly Activity Chart</h3>
            <canvas id="activity-chart" width="400" height="200"></canvas>
        </div>

        <!-- Tasks Table -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-2">Tasks</h3>
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table id="tasks-table" class="min-w-full bg-white border-collapse table-auto rounded-lg">
                    <thead class="bg-gray-200 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold border-b">Task Name</th>
                        <th class="px-6 py-3 text-left font-semibold border-b">Description</th>
                        <th class="px-6 py-3 text-left font-semibold border-b">List</th>
                        <th class="px-6 py-3 text-left font-semibold border-b">Created By</th>
                        <th class="px-6 py-3 text-left font-semibold border-b">Link</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
            <div id="tasks-pagination" class="mt-4"></div>
        </div>

        <!-- Comments Table -->
        <div>
            <h3 class="text-xl font-semibold mb-2">Comments</h3>
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table id="comments-table" class="min-w-full bg-white border-collapse table-auto rounded-lg">
                    <thead class="bg-gray-200 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold border-b">Comment</th>
                        <th class="px-6 py-3 text-left font-semibold border-b">Task</th>
                        <th class="px-6 py-3 text-left font-semibold border-b">List</th>
                        <th class="px-6 py-3 text-left font-semibold border-b">Created By</th>
                        <th class="px-6 py-3 text-left font-semibold border-b">Link</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
            <div id="comments-pagination" class="mt-4"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const monthPicker = document.getElementById('month-picker');
            const userFilter = document.getElementById('user-filter');
            const chartCtx = document.getElementById('activity-chart').getContext('2d');
            let activityChart;

            const today = new Date();
            const currentMonth = today.toISOString().slice(0, 7);
            monthPicker.value = currentMonth;

            const endpoints = [
                {
                    url: '{{ route('clickup.toxic.whole.monthly') }}',
                    tableId: 'users-table',
                    paginationId: 'users-pagination'
                },
                {
                    url: '{{ route('clickup.toxic.whole.tasks.monthly') }}',
                    tableId: 'tasks-table',
                    paginationId: 'tasks-pagination'
                },
                {
                    url: '{{ route('clickup.toxic.whole.comments.monthly') }}',
                    tableId: 'comments-table',
                    paginationId: 'comments-pagination'
                },
            ];

            loadInitialData(currentMonth);

            fetch('{{ route('clickup.users') }}')
                .then(response => response.json())
                .then(users => {
                    users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.text = user.username;
                        userFilter.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching users:', error));

            monthPicker.addEventListener('change', function () {
                const selectedMonth = monthPicker.value;
                const selectedUser = userFilter.value === 'all' ? [] : [userFilter.value];
                loadInitialData(selectedMonth, selectedUser);
            });

            userFilter.addEventListener('change', function () {
                const selectedUser = userFilter.value === 'all' ? [] : [userFilter.value];
                const selectedMonth = monthPicker.value;
                loadInitialData(selectedMonth, selectedUser);
            });

            function loadInitialData(month, users = []) {
                endpoints.forEach(endpoint => {
                    loadData(endpoint.url, endpoint.tableId, endpoint.paginationId, 1, month, users);
                });
                loadChartData(month); // Ensure this line is included to load chart data
            }

            function loadChartData(month) {
                fetch('{{ route('clickup.get.chart') }}?month=' + month)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Chart data:', data); // Log the fetched data for debugging

                        // Create an array of dates for the selected month
                        const daysInMonth = new Date(today.getFullYear(), new Date(month).getMonth() + 1, 0).getDate();
                        const dates = Array.from({ length: daysInMonth }, (_, i) =>
                            new Date(today.getFullYear(), new Date(month).getMonth(), i + 1).toISOString().split('T')[0]
                        );

                        const reportCounts = new Array(daysInMonth).fill(0); // Initialize report counts

                        data.forEach(item => {
                            const reportDate = item.created_at.split('T')[0];
                            const index = dates.indexOf(reportDate);
                            if (index !== -1) {
                                reportCounts[index] += item.reports; // Assuming reports is a number
                            }
                        });

                        // Check if activityChart already exists and destroy it
                        if (activityChart) {
                            activityChart.destroy();
                        }

                        // Create the new chart
                        activityChart = new Chart(chartCtx, {
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
                    })
                    .catch(error => {
                        console.error('Error fetching chart data:', error);
                    });
            }

            function loadData(url, tableId, paginationId, page, month, users = []) {
                let query = `${url}?page=${page}&month=${month}`;
                if (users.length > 0) {
                    query += `&users=${users.join(',')}`;
                }

                fetch(query)
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.task_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.task_description}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.list_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.created_by}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600"><a href="${item.url}" target="_blank">Link</a></td>
                                    </tr>`;
                            } else if (tableId === 'comments-table') {
                                tableBody.innerHTML += `
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.task_comments}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.task_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.list_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.username}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600"><a href="${item.url}" target="_blank">Link</a></td>
                                    </tr>`;
                            }
                        });

                        setupPagination(paginationDiv, data.links, url, tableId, paginationId, month, users);
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            function setupPagination(paginationDiv, links, url, tableId, paginationId, month, users = []) {
                paginationDiv.innerHTML = '';
                links.forEach(link => {
                    const pageButton = document.createElement('button');
                    pageButton.innerText = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
                    pageButton.className = `mx-1 px-3 py-1 border rounded-lg ${link.active ? 'bg-blue-500 text-white' : 'bg-white text-gray-700'}`;
                    pageButton.addEventListener('click', () => {
                        if (!link.active && link.url) {
                            const page = new URL(link.url).searchParams.get('page');
                            loadData(url, tableId, paginationId, page, month, users);
                        }
                    });
                    paginationDiv.appendChild(pageButton);
                });
            }
        });
    </script>
</x-app-layout>
