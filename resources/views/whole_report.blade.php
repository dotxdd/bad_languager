<x-app-layout>
    <div class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">ClickUp Full Report</h2>

        <!-- User Filter -->
        <div class="mb-6">
            <label for="user-filter" class="block text-lg font-semibold text-gray-700">Filter by Users:</label>
            <select id="user-filter" class="p-2 border border-gray-300 rounded-md shadow-sm w-full bg-white">
                <option value="all">Wszyscy</option>
            </select>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <!-- Users Table -->
            <h3 class="text-2xl font-semibold mb-4 text-blue-600">Users Full Report</h3>
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

            <!-- Tasks Table -->
            <h3 class="text-2xl font-semibold mt-8 mb-4 text-blue-600">Tasks Full Report</h3>
            <div class="overflow-x-auto">
                <table id="tasks-full-table" class="min-w-full divide-y divide-gray-200 border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">List</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div id="tasks-full-pagination" class="flex justify-end mt-4"></div>

            <!-- Comments Table -->
            <h3 class="text-2xl font-semibold mt-8 mb-4 text-blue-600">Comments Full Report</h3>
            <div class="overflow-x-auto">
                <table id="comments-full-table" class="min-w-full divide-y divide-gray-200 border-collapse">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">List</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div id="comments-full-pagination" class="flex justify-end mt-4"></div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const userFilter = document.getElementById('user-filter');

            const endpoints = [
                {
                    url: '{{ route('clickup.toxic.whole.users') }}',
                    tableId: 'users-full-table',
                    paginationId: 'users-full-pagination'
                },
                {
                    url: '{{ route('clickup.toxic.whole.tasks') }}',
                    tableId: 'tasks-full-table',
                    paginationId: 'tasks-full-pagination'
                },
                {
                    url: '{{ route('clickup.toxic.whole.comments') }}',
                    tableId: 'comments-full-table',
                    paginationId: 'comments-full-pagination'
                }
            ];

            // Fetch users and populate the user filter
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

            // Function to load data based on selected users
            function loadData(url, tableId, paginationId, page, users = []) {
                let query = `${url}?page=${page}`;
                if (users.length > 0) {
                    query += `&users=${users.join(',')}`; // Append user IDs to query
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.total_report_count}</td>
                                    </tr>`;
                            } else if (tableId === 'tasks-full-table') {
                                tableBody.innerHTML += `
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.task_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.task_description}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.list_name}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.created_by}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600"><a href="${item.url}" target="_blank">Link</a></td>
                                    </tr>`;
                            } else if (tableId === 'comments-full-table') {
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

                        setupPagination(paginationDiv, data.links, url, tableId, paginationId, users);
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            function setupPagination(paginationDiv, links, url, tableId, paginationId, users = []) {
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

            // Initially load the data for all tables
            endpoints.forEach(endpoint => loadData(endpoint.url, endpoint.tableId, endpoint.paginationId, 1));

            // Add event listener for user filter change
            userFilter.addEventListener('change', function () {
                const selectedUser = userFilter.value;
                const users = selectedUser === 'all' ? [] : [selectedUser];
                endpoints.forEach(endpoint => loadData(endpoint.url, endpoint.tableId, endpoint.paginationId, 1, users));
            });
        });
    </script>
</x-app-layout>
