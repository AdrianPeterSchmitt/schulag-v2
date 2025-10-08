<?php
/**
 * Swap Result Partial
 * 
 * HTMX-kompatibles Partial für Tausch-Ergebnisse
 */
?>
<div class="mt-4 p-4 rounded-lg <?= $success ? 'bg-green-50 border-2 border-green-500' : 'bg-red-50 border-2 border-red-500' ?>">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <?php if ($success): ?>
                <!-- Success Icon -->
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            <?php else: ?>
                <!-- Error Icon -->
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            <?php endif; ?>
        </div>
        <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium <?= $success ? 'text-green-800' : 'text-red-800' ?>">
                <?= $success ? 'Erfolg!' : 'Fehler!' ?>
            </h3>
            <div class="mt-2 text-sm <?= $success ? 'text-green-700' : 'text-red-700' ?>">
                <p><?= esc($message) ?></p>
            </div>
            
            <?php if ($success && isset($allocations) && !empty($allocations)): ?>
                <!-- Zeige aktualisierte Zuteilungen -->
                <div class="mt-4">
                    <h4 class="text-sm font-semibold text-green-900 mb-2">Aktualisierte Zuteilungen:</h4>
                    <div class="space-y-2">
                        <?php foreach ($allocations as $allocation): ?>
                            <div class="bg-white p-3 rounded border border-green-200">
                                <p class="text-sm text-gray-900">
                                    <span class="font-medium">Schüler-ID:</span> <?= esc($allocation['student_id']) ?>
                                    <br>
                                    <span class="font-medium">AG-ID:</span> <?= esc($allocation['offer_id'] ?? 'Keine') ?>
                                    <br>
                                    <span class="font-medium">Status:</span> 
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <?= esc($allocation['status']) ?>
                                    </span>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button type="button" 
                        onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"
                        class="inline-flex rounded-md p-1.5 <?= $success ? 'text-green-500 hover:bg-green-100' : 'text-red-500 hover:bg-red-100' ?> focus:outline-none focus:ring-2 focus:ring-offset-2 <?= $success ? 'focus:ring-green-500' : 'focus:ring-red-500' ?>">
                    <span class="sr-only">Schließen</span>
                    <!-- X Icon -->
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

