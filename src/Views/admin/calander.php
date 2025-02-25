<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modern Admin Dashboard</title>
  <link rel="icon" href="/assets/Logo - Copie.png"/>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
    <body class="min-h-screen bg-gray-50" x-data="dashboardData">

        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../templates/aside.php'; ?>

        <!-- Top Bar -->
        <div class="sm:ml-56 mb-8 bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="text-xl font-bold italic">
                Tableau de Bord Super Admin
            </div>
            <div class="flex items-center gap-4">
                <!-- <button class="relative p-2">
                    <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-xs rounded-full">3</span>
                    &#x1F514;  
                </button> -->
                <div class="flex items-center gap-2">
                    <img src="<?= $_SESSION['user']['avatar']; ?>" alt="Admin" class="w-8 h-8 rounded-full">
                    <div>
                        <p class="text-sm font-medium"><?= $_SESSION['user']['name']; ?></p>
                        <p class="text-xs text-gray-500">Super Admin</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- contenu principale -->
        <div class="sm:ml-56 p-6">
            <div x-data="calendar()" class="max-w-md mx-auto p-4 bg-gray-800 text-white rounded-lg shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <button 
                        @click="previousMonth" 
                        class="px-4 py-2 bg-gray-700 rounded hover:bg-gray-600">
                        Précédent
                    </button>
                    <h2 class="text-lg font-semibold">
                        <span x-text="months[currentMonth]"></span> <span x-text="currentYear"></span>
                    </h2>
                    <button 
                        @click="nextMonth" 
                        class="px-4 py-2 bg-gray-700 rounded hover:bg-gray-600">
                        Suivant
                    </button>
                </div>
                <div class="grid grid-cols-7 gap-2 text-center text-gray-300">
                    <div class="font-semibold">Dim</div>
                    <div class="font-semibold">Lun</div>
                    <div class="font-semibold">Mar</div>
                    <div class="font-semibold">Mer</div>
                    <div class="font-semibold">Jeu</div>
                    <div class="font-semibold">Ven</div>
                    <div class="font-semibold">Sam</div>
                </div>
                <div class="grid grid-cols-7 gap-2 mt-2 text-center">
                    <template x-for="blankDay in blankDays">
                        <div class="p-4"></div>
                    </template>
                    <template x-for="day in days">
                        <div 
                            @click="selectDate(day)" 
                            class="p-4 rounded cursor-pointer" 
                            :class="{'bg-gray-600': isToday(day), 'bg-blue-600': isSelected(day)}">
                            <span x-text="day"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <script>
            function calendar() {
                return {
                    currentMonth: new Date().getMonth(),
                    currentYear: new Date().getFullYear(),
                    days: [],
                    blankDays: [],
                    selectedDate: null,
                    months: [
                        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
                    ],
                    init() {
                        this.calculateDays();
                    },
                    calculateDays() {
                        const startOfMonth = new Date(this.currentYear, this.currentMonth, 1);
                        const endOfMonth = new Date(this.currentYear, this.currentMonth + 1, 0);

                        this.blankDays = Array.from({ length: startOfMonth.getDay() });
                        this.days = Array.from({ length: endOfMonth.getDate() }, (_, i) => i + 1);
                    },
                    previousMonth() {
                        if (this.currentMonth === 0) {
                            this.currentMonth = 11;
                            this.currentYear--;
                        } else {
                            this.currentMonth--;
                        }
                        this.calculateDays();
                    },
                    nextMonth() {
                        if (this.currentMonth === 11) {
                            this.currentMonth = 0;
                            this.currentYear++;
                        } else {
                            this.currentMonth++;
                        }
                        this.calculateDays();
                    },
                    selectDate(day) {
                        this.selectedDate = new Date(this.currentYear, this.currentMonth, day);
                    },
                    isToday(day) {
                        const today = new Date();
                        return (
                            day === today.getDate() &&
                            this.currentMonth === today.getMonth() &&
                            this.currentYear === today.getFullYear()
                        );
                    },
                    isSelected(day) {
                        return this.selectedDate &&
                            this.selectedDate.getDate() === day &&
                            this.selectedDate.getMonth() === this.currentMonth &&
                            this.selectedDate.getFullYear() === this.currentYear;
                    }
                }
            }
        </script>
    </body>
</html>