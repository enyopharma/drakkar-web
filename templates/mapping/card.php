<div class="card">
    <h5 class="card-header">
        <?= $alignment['accession'] ?>
    </h5>
    <div class="card-body">
        <svg class="alignment" width="100%" height="30">
            <text x="0" y="30" font-size="10">
                1
            </text>
            <text x="100%" y="10" font-size="10" text-anchor="end">
                <?= $alignment['maxwidth'] ?>
            </text>
            <text x="<?= $alignment['pstart'] ?>%" y="10" font-size="10">
                <?= $alignment['start'] ?>
            </text>
            <text x="<?= $alignment['pstop'] ?>%" y="30" font-size="10" text-anchor="end">
                <?= $alignment['stop'] ?>
            </text>
            <rect x="0" y="16" width="100%" height="2"></rect>
            <rect
                class="<?= $type ?>"
                x="<?= $alignment['pstart'] ?>%" y="14"
                width="<?= $alignment['pwidth'] ?>%" height="6"
            ></rect>
        </svg>
        <?php foreach($alignment['occurences'] as $occurence): ?>
        <svg class="alignment" width="100%" height="30">
            <text x="<?= $occurence['pstart'] ?>%" y="10" font-size="10">
                <?= $occurence['start'] ?>
            </text>
            <text x="<?= $occurence['pstop'] ?>%" y="30" font-size="10" text-anchor="end">
                <?= $occurence['stop'] ?>
            </text>
            <rect x="0" y="16" width="100%" height="2"></rect>
            <rect
                class="<?= $type ?>"
                x="<?= $occurence['pstart'] ?>%" y="14"
                width="<?= $occurence['pwidth'] ?>%" height="6"
            ></rect>
        </svg>
        <?php endforeach ?>
    </div>
</div>
