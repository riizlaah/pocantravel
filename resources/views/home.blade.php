<x-base-layout title="Home">
    @csrf
    <div class="p-2">
        <div class="my-4">
            <div class="mx-auto w-2/3 text-center">
                <h1 class="text-xl font-semibold">Halo, {{ auth()->user()->name }}!</h1>
                <span>Mau pesan tiket apa hari ini?</span>
            </div>
        </div>
        <div class="flex flex-col md:flex-row gap-2 w-full">
            <div class="flex-grow lg:border-e lg:border-gray-200 px-2" open>
                <h2 class="text-sky-700">Tiket Terbaru</h2>
                <div class="flex gap-2">
                    <input type="text" name="search" id="search"
                        class="flex flex-grow p-1 rounded-md border border-gray-200 outline-none focus:border-gray-400 focus:shadow-md transition">
                    <input type="date" name="date" id="date" class="p-1 rounded-md border border-gray-200">
                </div>
                <div id="latest_schedules" class="grid grid-cols-1 md:grid-cols-2 gap-2 p-2 mb-2"></div>
                <div class="flex justify-center items-center">
                    <div class="rounded-md border border-gray-400" id="schedules_pagination"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function query(selector) {
            return document.querySelector(selector);
        }

        let searchTimeout = 0;
        let savedIcon;
        let unsavedIcon;
        const searchInp = query("#search");
        const csrfToken = query("input[name=\"_token\"]");
        const date = query("#date");

        async function loadAndAttachIcons() {
            const eyeRes = await fetch("/icons/saved.svg");
            const eyeOffRes = await fetch("/icons/unsaved.svg");
            savedIcon = await eyeRes.text();
            unsavedIcon = await eyeOffRes.text();
        }

        async function get_schedules_data(search = "", dateFilter = "", page = 1, size = 4) {
            let url = `/schedules?page=${page}&size=${size}`;
            if (search.trim().length > 0) {
                url += "&s=" + encodeURI(search);
            }
            if (dateFilter.trim().length > 0) {
                url += "&date=" + dateFilter;
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
                div.innerHTML = `<div class="flex items-center justify-between mb-3">
                <div>
                    <span class="font-bold text-xl">${schedule.departure_point} - ${schedule.final_destination}</span>
                    <span class="text-gray-500">${formattedDate} | ${schedule.time.substring(0,5)}</span>
                </div>
                <button type="button" onclick="toggle_save(this, ${schedule.id})">
                    ${schedule.is_saved ? savedIcon : unsavedIcon}
                </button>
            </div>
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
                pages +=
                    `<button type="button" class="border-e border-gray-400 px-2 py-1" onclick="get_schedules(${i})">${i}</button>`;
            }
            if (data.pagination.page > 1) {
                elements +=
                    `<button type="button" class="border-e border-gray-400 px-2 py-1" onclick="get_schedules(1)">&lt;</button>`;
            }
            elements += pages;
            if (data.pagination.page < data.pagination.max_page) {
                elements +=
                    `<button type="button" class="px-2 py-1" onclick="get_schedules(${data.pagination.max_page})">&gt;</button>`;
            }
            pagination.innerHTML = elements;
        }

        async function toggle_save(btn, scheduleId) {
            const res = await fetch(`/toggle-save-schedule/${scheduleId}`, {
                method: 'POST',
                headers: {
                    "X-CSRF-TOKEN": csrfToken.value
                }
            });
            const json = await res.json();
            if(!res.ok) alert(json.message ?? "Gagal menyimpan/menghapus tiket");
            btn.innerHTML = (json.is_saved ?? false) ? savedIcon : unsavedIcon;
        }

        loadAndAttachIcons().then(() => {
            get_schedules();
        });

        function try_search_schedules() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                get_schedules();
            }, 500);
        }

        searchInp.oninput = () => {
            try_search_schedules();
        };
        date.onchange = () => {
            try_search_schedules();
        };

        async function buy_ticket(id) {
            if (!confirm("Yakin ingin membeli tiket ini?")) return;
            const res = await fetch(`/ticket/${id}/buy`, {
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
