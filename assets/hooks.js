import { useCallback, useState } from 'react';

export function request(url, method = 'GET', data = null) {
    return fetch(url, {
        method, body: data ? JSON.stringify(data) : null, headers: {
            'Accept': 'application/ld+json',
            'Content-Type': 'application/json'
        }
    }).then(response => response.ok ? response.json() : Promise.reject(response));
}

export function useFetch(url) {
    const [loading, setLoading] = useState(false);
    const [data, setData] = useState([]);
    const [total, setTotal] = useState(0);
    const [next, setNext] = useState(null);

    const load = useCallback(() => {
        setLoading(true);

        request(next || url).then(response => {
            setData(data => data.concat(response['hydra:member']));
            setTotal(response['hydra:totalItems']);

            if (response['hydra:view'] && response['hydra:view']['hydra:next']) {
                setNext(response['hydra:view']['hydra:next']);
            } else {
                setNext(null);
            }
        }).catch(
            error => error.json().then(data => console.error(data))
        ).finally(() => setLoading(false))
    }, [url, next]);

    return { data, total, loading, load, next: next !== null };
}
