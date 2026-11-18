<x-base-layout>
    @csrf
    <div class="flex w-full py-3 px-5 gap-4 border-b border-gray-200">
        <span class="font-bold text-sky-700">PO CAN Travel</span>
        <div class="flex-grow"></div>
        <a href="/logout" class="font-medium text-red-500" onclick="return confirm('Yakin ingin logout?')">Logout</a>
    </div>
    <div class="p-2">

      <div class="my-4">
          <div class="mx-auto w-2/3 text-center">
              <h1 class="text-xl font-semibold">Halo, {{ auth()->user()->name }}!</h1>
              <span>Mau pesan tiket apa hari ini?</span>
          </div>
      </div>
      <div class="flex flex-col md:flex-row gap-2 w-full">
          <details class="flex-grow lg:border-e lg:border-gray-200 px-2">
              <summary class="text-sky-700">Tiket Terbaru</summary>
              <div class="flex gap-2">
                  <input type="text" name="search" id="search"
                      class="flex flex-grow p-1 rounded-md border border-gray-200 outline-none focus:border-gray-400 focus:shadow-md transition">
                  <input type="date" name="date" id="date" class="p-1 rounded-md border border-gray-200">
              </div>
              <div id="latest_schedules" class="flex flex-col gap-2 p-2 mb-2"></div>
              <div class="flex justify-center items-center">
                  <div class="rounded-md border border-gray-400" id="schedules_pagination"></div>
              </div>
          </details>
          <details class="flex-grow px-2">
              <summary class="text-sky-700">Tiket Yang Dibeli</summary>
              <div class="flex gap-2">
                  <input type="text" name="search" id="search2"
                      class="flex flex-grow p-1 rounded-md border border-gray-200 outline-none focus:border-gray-400 focus:shadow-md transition">
                  <input type="date" name="date" id="date2" class="p-1 rounded-md border border-gray-200">
              </div>
              <div id="purchased_tickets" class="flex flex-col gap-2 p-2 mb-2"></div>
              <div class="flex justify-center items-center">
                  <div class="rounded-md border border-gray-400" id="tickets_pagination"></div>
              </div>
          </details>
      </div>
    </div>
    <script>
        function query(selector) {
            return document.querySelector(selector);
        }

        let searchTimeout = 0;
        let searchTimeout2 = 0;
        const searchInp = query("#search");
        const searchInp2 = query("#search2");
        const csrfToken = query("input[name=\"_token\"]");
        const date = query("#date");
        const date2 = query("#date2");

        async function get_schedules_data(search = "", dateFilter = "", page = 1, size = 4) {
            let url = `/get-schedules?page=${page}&size=${size}`;
            if (search.trim().length > 0) {
                url += "&s=" + encodeURI(search);
            }
            if (dateFilter.trim().length > 0) {
                url += "&date=" + dateFilter
            }

            const res = await fetch(url);
            if (res.ok) return await res.json();
            return [];
        }
        async function get_schedules(page = 1, size = 4) {
            const data = await get_schedules_data(searchInp.value, date.value, page, size);
            const list = query('#latest_schedules');
            const pagination = query("#schedules_pagination");
            list.innerHTML = "";
            data.data.forEach(schedule => {
                let div = document.createElement('div');
                div.className = "flex flex-col border border-gray-300 rounded p-2";
                const formattedDate = new Intl.DateTimeFormat('id-ID', {
                    weekday: "long",
                    day: "numeric",
                    month: "short",
                    year: "numeric"
                }).format(new Date(schedule.date))
                const formattedPrice = new Intl.NumberFormat('id-ID', {
                    style: "currency",
                    currency: "IDR"
                }).format(schedule.price);
                div.innerHTML = `<span class="font-bold text-xl">${schedule.departure_point} - ${schedule.final_destination}</span>
          <span class="text-gray-500 mb-3">${formattedDate} | ${schedule.time.substring(0,5)}</span>
          <div class="flex gap-2 items-center">
            <div class="flex-grow">
              <div>
                <span class="text-md font-semibold">${formattedPrice}</span>
                |
                <span class="text-md">Bus ${schedule.bus_name}</span>
                |
                <span class="text-md">${schedule.max_slot - schedule.ticket_purchased} slot tersisa</span>
              </div>
              <p class="text-gray-600">${schedule.description}</p>
            </div>
            <button type="button" class="bg-sky-600 text-white rounded-md py-2 px-6" onclick="buy_ticket(${schedule.id})">Beli</button>
          </div>
          `;
                list.appendChild(div);
            });

            let elements = '';
            let pages = '';
            for (let i = 1; i <= data.pagination.max_page; i++) {
                pages += `<button type="button" class="border-e border-gray-400 px-2 py-1" onclick="get_schedules(${i})">${i}</button>`;
            }
            if (data.pagination.page > 1) {
                elements += `<button type="button" class="border-e border-gray-400 px-2 py-1" onclick="get_schedules(1)">&lt;</button>`;
            }
            elements += pages;
            if (data.pagination.page < data.pagination.max_page) {
                elements += `<button type="button" class="px-2 py-1" onclick="get_schedules(${data.pagination.max_page})">&gt;</button>`;
            }
            pagination.innerHTML = elements;
        }

        async function get_tickets_data(search = "", dateFilter = "", page = 1, size = 4) {
            let url = `/get-tickets?page=${page}&size=${size}`;
            if (search.trim().length > 0) {
                url += "?s=" + encodeURI(search);
            }
            if (dateFilter.trim().length > 0) {
                url += "&date=" + dateFilter
            }
            const res = await fetch(url);
            if (res.ok) return await res.json();
            return [];
        }
        async function get_tickets(page = 1, size = 4) {
            const data = await get_tickets_data(searchInp2.value, date2.value, page, size);
            const list = query('#purchased_tickets');
            const pagination = query('#tickets_pagination');
            list.innerHTML = "";
            data.data.forEach(schedule => {
                let div = document.createElement('div');
                div.className = "flex flex-col border border-gray-300 rounded p-2";
                const formattedDate = new Intl.DateTimeFormat('id-ID', {
                    weekday: "long",
                    day: "numeric",
                    month: "short",
                    year: "numeric"
                }).format(new Date(schedule.date))
                const formattedPrice = new Intl.NumberFormat('id-ID', {
                    style: "currency",
                    currency: "IDR"
                }).format(schedule.price);
                div.innerHTML = `<span class="font-bold text-xl">${schedule.departure_point} - ${schedule.final_destination}</span>
          <span class="text-gray-500 mb-3">${formattedDate} | ${schedule.time.substring(0,5)}</span>
          <div class="flex gap-2 items-center">
            <div class="flex-grow">
              <div>
                <span class="text-md font-semibold">${formattedPrice}</span>
                |
                <span class="text-md">Bus ${schedule.bus_name}</span>
              </div>
              <p class="text-gray-600">${schedule.description}</p>
            </div>
            <span class="text-lg text-sky-700 font-bold font-mono">${schedule.code}</span>
          </div>`;
                list.appendChild(div);
            });

            let elements = '';
            let pages = '';
            for (let i = 1; i <= data.pagination.max_page; i++) {
                pages += `<button type="button" class="border-e border-gray-400 px-2 py-1" onclick="get_tickets(${i})">${i}</button>`;
            }
            if (data.pagination.page > 1) {
                elements += `<button type="button" class="border-e border-gray-400 px-2 py-1" onclick="get_tickets(1)">&lt;</button>`;
            }
            elements += pages;
            if (data.pagination.page < data.pagination.max_page) {
                elements += `<button type="button" class="px-2 py-1" onclick="get_tickets(${data.pagination.max_page})">&gt;</button>`;
            }
            pagination.innerHTML = elements;
        }

        get_schedules();
        get_tickets();

        function try_search_schedules() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                get_schedules();
            }, 500);
        }

        function try_search_tickets() {
            clearTimeout(searchTimeout2);
            searchTimeout2 = setTimeout(() => {
                get_tickets();
            }, 500);
        }
        searchInp.oninput = () => {
            try_search_schedules();
        };
        date.onchange = () => {
            try_search_schedules();
        };
        searchInp2.oninput = () => {
            try_search_tickets();
        };
        date2.onchange = () => {
            try_search_tickets();
        }

        async function buy_ticket(id) {
            if (!confirm("Yakin ingin membeli tiket ini?")) return;
            const res = await fetch("/buy-ticket/" + id, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken.value
                }
            });
            const json = await res.json();
            alert(res.ok ? (json.message ?? 'Sukses') : (json.message ?? 'Gagal'));
            document.location.reload();
        }
    </script>
</x-base-layout>
