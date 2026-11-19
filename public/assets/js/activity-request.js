const activityRequestDialog = document.querySelector('#activity-request-dialog');
const activityRequestForm = activityRequestDialog.querySelector('.activity-request-form');

document.querySelector('main').addEventListener('click', (event) => {
    const button = event.target.closest('.activity-request-open');
    if (!button) return;

    activityRequestForm.reset();
    activityRequestDialog.querySelector('h2').textContent = button.dataset.activityName;
    activityRequestForm.elements.namedItem('activity_id').value = button.dataset.activityId;
    activityRequestForm.elements.namedItem('adults').max = button.dataset.activityCapacity;
    activityRequestForm.elements.namedItem('children').max = button.dataset.activityCapacity;
    activityRequestDialog.showModal();
});
