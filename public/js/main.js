const statistics = document.getElementById('statistics');

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
}
