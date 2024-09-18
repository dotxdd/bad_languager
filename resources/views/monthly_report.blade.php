<x-app-layout>
    <div class="container mx-auto py-8">
        <h2 class="text-2xl font-bold mb-4">Monthly ClickUp Report</h2>

        <div class="mb-6">
            <label for="month-picker" class="block text-sm font-medium text-gray-700">Select Month:</label>
            <input type="month" id="month-picker" class="mt-1 block w-64 p-2 border border-gray-300 rounded-lg shadow-sm">
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-2">Toxic Users</h3>
            <table id="users-table" class="min-w-full bg-white border rounded-lg">
                <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reports</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200"></tbody>
            </table>
            <div id="users-pagination" class="mt-4"></div>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-2">Tasks</h3>
            <table id="tasks-table" class="min-w-full bg-white border rounded-lg">
                <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">List</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200"></tbody>
            </table>
            <div id="tasks-pagination" class="mt-4"></div>
        </div>

        <div>
            <h3 class="text-xl font-semibold mb-2">Comments</h3>
            <table id="comments-table" class="min-w-full bg-white border rounded-lg">
                <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">List</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200"></tbody>
            </table>
            <div id="comments-pagination" class="mt-4"></div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const monthPicker = document.getElementById('month-picker');

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
                }
            ];

            endpoints.forEach(endpoint => {
                loadData(endpoint.url, endpoint.tableId, endpoint.paginationId, 1, currentMonth);
            });

            monthPicker.addEventListener('change', function () {
                const selectedMonth = monthPicker.value;
                endpoints.forEach(endpoint => {
                    loadData(endpoint.url, endpoint.tableId, endpoint.paginationId, 1, selectedMonth);
                });
            });

            function loadData(url, tableId, paginationId, page, month) {
                fetch(`${url}?page=${page}&month=${month}`)
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.comment}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.task_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.list_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.created_by}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600"><a href="${item.url}" target="_blank">Link</a></td>
                                    </tr>`;
                            }
                        });

                        setupPagination(paginationDiv, data.links, url, tableId, paginationId, month);
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            function setupPagination(paginationDiv, links, url, tableId, paginationId, month) {
                paginationDiv.innerHTML = '';
                links.forEach(link => {
                    const pageButton = document.createElement('button');
                    pageButton.innerHTML = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
                    pageButton.className = `mx-1 px-3 py-1 border rounded-lg ${link.active ? 'bg-blue-500 text-white' : 'bg-white text-gray-700'}`;
                    pageButton.addEventListener('click', () => {
                        if (!link.active && link.url) {
                            const page = new URL(link.url).searchParams.get('page');
                            loadData(url, tableId, paginationId, page, month);
                        }
                    });
                    paginationDiv.appendChild(pageButton);
                });
            }
        });
    </script>
</x-app-layout>
