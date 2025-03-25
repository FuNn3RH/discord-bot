const $ = document

const rows = $.querySelectorAll('.table-row')
const filterBtns = $.querySelectorAll('.btn-filter')

filterBtns.forEach(btn => {
    btn.addEventListener('change', () => {
        const filter = btn.value

        filterRows(filter)
    })
})

function filterRows(mode) {
    rows.forEach(row => {
        const status = row.dataset.paid

        if (mode === 'all') {
            row.classList.remove('d-none')
            return
        }

        if (status === mode) {
            row.classList.remove('d-none')
        } else {
            row.classList.add('d-none')
        }
    })
}
