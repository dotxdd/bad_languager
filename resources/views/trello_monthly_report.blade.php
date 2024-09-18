<x-app-layout>
    <div class="container mx-auto mt-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Trello Monthly Report</h2>

        <div class="mb-6">
            <label for="date-picker" class="block text-lg font-semibold text-gray-700">Select Month and Year:</label>
            <select id="date-picker" class="p-2 border border-gray-300 rounded-md shadow-sm w-full bg-white">
            </select>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-2xl font-semibold mb-4 text-blue-600">Users Monthly Report</h3>
            <div class="overflow-x-auto">
                <table id="users-table" class="min-w-full divide-y divide-gray-200 border-collapse">
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
            <div id="users-pagination" class="flex justify-end mt-4"></div>

            <h3 class="text-2xl font-semibold mt-8 mb-4 text-blue-600">Tasks Monthly Report</h3>
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

            <h3 class="text-2xl font-semibold mt-8 mb-4 text-blue-600">Comments Monthly Report</h3>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const datePicker = document.getElementById('date-picker');

            const currentYear = new Date().getFullYear();
            const currentMonth = new Date().getMonth() + 1;

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

            generateDateOptions();

            datePicker.value = `${currentYear}-${String(currentMonth).padStart(2, '0')}`;

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

            function updateReports(date) {
                endpoints.forEach(endpoint => {
                    loadData(endpoint.url, endpoint.tableId, endpoint.paginationId, 1, date);
                });
            }

            function loadData(url, tableId, paginationId, page, date) {
                fetch(`${url}?page=${page}&month=${date}`)
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
                                loadData(url, tableId, paginationId, page, date);
                            });
                        });
                    });
            }

            updateReports(datePicker.value);

            datePicker.addEventListener('change', () => {
                updateReports(datePicker.value);
            });
        });
    </script>
</x-app-layout>
