<?php
// Helper: Check if student has complete choices
$hasNoParticipation = false;
$normalChoicesCount = 0;
$choicesByPriority = ['1' => null, '2' => null, '3' => null];

foreach ($student['choices'] as $choice) {
    if ($choice['priority'] === 'no_participation') {
        $hasNoParticipation = true;
    } elseif (in_array($choice['priority'], ['1', '2', '3']) && $choice['offer_id'] !== null) {
        $choicesByPriority[$choice['priority']] = $choice['offer_id'];
        $normalChoicesCount++;
    }
}

$isComplete = $hasNoParticipation || $normalChoicesCount === 3;
$statusColor = $isComplete ? 'green' : 'orange';
$statusIcon = $isComplete ? '✅' : '⏳';
?>

<div id="student-<?= $student['id'] ?>" class="bg-white rounded-xl shadow-md border-l-4 border-<?= $statusColor ?>-500">
    <div class="p-6">
        <!-- Student Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center text-white font-bold">
                    <?= esc(substr($student['name'], 0, 2)) ?>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900"><?= esc($student['name']) ?></h3>
                    <p class="text-sm text-gray-600">
                        <?= esc($student['typ_gl']) ?>
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <span class="text-2xl"><?= $statusIcon ?></span>
                <span class="text-sm font-medium text-<?= $statusColor ?>-600">
                    <?= $isComplete ? 'Vollständig' : 'In Bearbeitung' ?>
                </span>
            </div>
        </div>

        <!-- Choices Form -->
        <form hx-post="<?= base_url('klassen/' . $student['klasse_id'] . '/choices') ?>"
              hx-headers='{"X-CSRF-TOKEN": "<?= csrf_hash() ?>"}'
              hx-target="#student-<?= $student['id'] ?>"
              hx-swap="outerHTML"
              class="space-y-4">
            
            <input type="hidden" name="student_id" value="<?= $student['id'] ?>">

            <!-- No Participation Option -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" 
                       id="no_participation_<?= $student['id'] ?>"
                       name="no_participation" 
                       value="1"
                       <?= $hasNoParticipation ? 'checked' : '' ?>
                       onchange="toggleChoices(this, '<?= $student['id'] ?>')"
                       class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                <label for="no_participation_<?= $student['id'] ?>" class="text-sm font-medium text-gray-700">
                    Nimmt nicht an AGs teil
                </label>
            </div>

            <!-- AG Choices (disabled if no participation) -->
            <div id="choices_<?= $student['id'] ?>" class="<?= $hasNoParticipation ? 'opacity-50 pointer-events-none' : '' ?>">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <?= $i ?>. Wunsch
                            </label>
                            <select name="choice_<?= $i ?>" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">AG auswählen...</option>
                                <?php foreach ($student['available_offers'] as $offer): ?>
                                    <option value="<?= $offer['id'] ?>" 
                                            <?= $choicesByPriority[$i] == $offer['id'] ? 'selected' : '' ?>
                                            <?= in_array($offer['id'], $choicesByPriority) && $choicesByPriority[$i] != $offer['id'] ? 'disabled' : '' ?>>
                                        <?= esc($offer['club']['titel']) ?>
                                        (<?= $offer['capacity'] ?> Plätze)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endfor; ?>
                </div>
                
                <!-- Available AGs Info -->
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-600">
                        <strong>Verfügbare AGs:</strong> 
                        <?= count($student['available_offers']) ?> AGs stehen zur Verfügung
                        <?php if (count($student['available_offers']) < 3): ?>
                            <span class="text-orange-600">(Weniger als 3 AGs verfügbar)</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition flex items-center space-x-2">
                    <span class="htmx-indicator">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span>Speichern</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleChoices(checkbox, studentId) {
    const choicesDiv = document.getElementById('choices_' + studentId);
    const choices = choicesDiv.querySelectorAll('select');
    
    if (checkbox.checked) {
        choicesDiv.style.opacity = '0.5';
        choicesDiv.style.pointerEvents = 'none';
        choices.forEach(select => select.selectedIndex = 0);
    } else {
        choicesDiv.style.opacity = '1';
        choicesDiv.style.pointerEvents = 'auto';
    }
}
</script>
