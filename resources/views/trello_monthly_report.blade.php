<x-app-layout>
    <div class="container mx-auto mt-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Trello Monthly Report</h2>

        <!-- Datepicker -->
        <div class="mb-6">
            <label for="month-picker" class="block text-lg font-semibold text-gray-700">Select Date:</label>
            <input id="month-picker" type="date" class="mt-2 p-2 border border-gray-300 rounded-md shadow-sm w-full bg-white">
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <!-- Tabela dla użytkowników -->
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

            <!-- Tabela dla zadań -->
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

            <!-- Tabela dla komentarzy -->
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
            const monthPicker = document.getElementById('month-picker');
            console.log('t');
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

            function updateReports(date) {
                endpoints.forEach(endpoint => {
                    loadData(endpoint.url, endpoint.tableId, endpoint.paginationId, 1, date);
                });
            }

            function loadData(url, tableId, paginationId, page, date) {
                fetch(`${url}?page=${page}&date=${date}`)
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

                        setupPagination(paginationDiv, data.links, url, tableId, paginationId, date);
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            function setupPagination(paginationDiv, links, url, tableId, paginationId, date) {
                paginationDiv.innerHTML = '';
                links.forEach(link => {
                    const pageButton = document.createElement('button');
                    pageButton.innerText = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
                    pageButton.className = `mx-1 px-3 py-1 border rounded-lg ${link.active ? 'bg-blue-500 text-white' : 'bg-white text-gray-700'}`;
                    pageButton.addEventListener('click', () => {
                        if (!link.active && link.url) {
                            const page = new URL(link.url).searchParams.get('page');
                            loadData(url, tableId, paginationId, page, date);
                        }
                    });
                    paginationDiv.appendChild(pageButton);
                });
            }

            monthPicker.addEventListener('change', function () {
                const selectedDate = new Date(this.value);
                const firstDayOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
                const formattedDate = firstDayOfMonth.toISOString().split('T')[0];
                updateReports(formattedDate);
            });

            const currentDate = new Date();
            const firstDayOfCurrentMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            monthPicker.value = firstDayOfCurrentMonth.toISOString().split('T')[0];
            updateReports(firstDayOfCurrentMonth.toISOString().split('T')[0]);
        });
    </script>
</x-app-layout>
