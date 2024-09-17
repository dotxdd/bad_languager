<x-app-layout>
    <div class="container mx-auto mt-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Trello Full Report</h2>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <!-- Tabela dla użytkowników -->
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

            <!-- Tabela dla zadań -->
            <h3 class="text-2xl font-semibold mt-8 mb-4 text-blue-600">Tasks Full Report</h3>
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
            <h3 class="text-2xl font-semibold mt-8 mb-4 text-blue-600">Comments Full Report</h3>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
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

            endpoints.forEach(endpoint => {
                loadData(endpoint.url, endpoint.tableId, endpoint.paginationId, 1);
            });

            function loadData(url, tableId, paginationId, page) {
                fetch(`${url}?page=${page}`)
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

                        setupPagination(paginationDiv, data.links, url, tableId, paginationId);
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            function setupPagination(paginationDiv, links, url, tableId, paginationId) {
                paginationDiv.innerHTML = '';
                links.forEach(link => {
                    const pageButton = document.createElement('button');
                    pageButton.innerText = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
                    pageButton.className = `mx-1 px-3 py-1 border rounded-lg ${link.active ? 'bg-blue-500 text-white' : 'bg-white text-gray-700'}`;
                    pageButton.addEventListener('click', () => {
                        if (!link.active && link.url) {
                            const page = new URL(link.url).searchParams.get('page');
                            loadData(url, tableId, paginationId, page);
                        }
                    });
                    paginationDiv.appendChild(pageButton);
                });
            }
        });
    </script>
</x-app-layout>
