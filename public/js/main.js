const statistics = document.getElementById('statistics');
const elements = document.getElementById('elements');

if(statistics){
    statistics.addEventListener('click', e => {
        if(e.target.className === 'btn btn-danger'){
           if(confirm('Are you sure to delete the article?')){
                const id = e.target.getAttribute('data-id');
                console.log("statistic id: ", id);
                fetch(`/statistica/rete/delete/${id}`, {
                    method: 'DELETE'
                }).then(res => window.location.reload());
            }
        }
    } ); 
} else if (netelements){
    netelements.addEventListener('click', e => {
        if(e.target.className === 'btn btn-danger'){
           if(confirm('Are you sure to delete the Network Element?')){
                const id = e.target.getAttribute('data-id');
                console.log("Network Element id: ", id);
                fetch(`/network/element/delete/${id}`, {
                    method: 'DELETE'
                }).then(res => window.location.reload());
            }
        }
    } ); 
}
