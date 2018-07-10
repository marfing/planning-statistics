const statistics = document.getElementById('statistics');
const netelements = document.getElementById('netelements');
const cleancsv = document.getElementById("cleancsv");

console.log('main js loaded');
console.log(cleancsv);

if(cleancsv){
    console.log('cleancsv id found');
    cleancsv.addEventListener('click', e => {
        if(e.target.className === 'btn btn-danger'){
        if(confirm('Are you sure to delete csv files?')){
                const id = e.target.getAttribute('data-id');
                fetch(`/network/admin/csvdelete/${id}`, {
                    method: 'DELETE'
                }).then(res => window.location.reload());
            }
        }
    });
}   

if(statistics){
    statistics.addEventListener('click', e => {
        if(e.target.className === 'btn btn-danger'){
           if(confirm('Are you sure to delete statistic?')){
                const id = e.target.getAttribute('data-id');
                fetch(`/statistica/rete/delete/${id}`, {
                    method: 'DELETE'
                }).then(res => window.location.reload());
            }
        }
    } ); 
} else if (netelements){
    netelements.addEventListener('click', e => {
        if (e.target.className === 'btn btn-danger'){
            if(confirm('Are you sure to delete the Network Element?')){
                 const id = e.target.getAttribute('data-id');
                 fetch(`/network/element/delete/${id}`, {
                     method: 'DELETE'
                 }).then(res => window.location.reload());
            }
        }
    } ); 
} 



