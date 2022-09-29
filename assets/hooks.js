import { useCallback, useState } from 'react';

export function request(url, method = 'GET', data = null) {
    return fetch(url, {
        method, body: data ? JSON.stringify(data) : null, headers: {
            'Accept': 'application/ld+json',
            'Content-Type': 'application/json'
        }
    }).then(response => {
        if (response.status === 204) {
            return null;
        }

        return response.ok ? response.json() : Promise.reject(response);
    });
}

export function useFetch(url, method = 'GET') {
    const [loading, setLoading] = useState(false);
    const [data, setData] = useState([]);
    const [total, setTotal] = useState(0);
    const [next, setNext] = useState(null);

    const load = useCallback(() => {
        setLoading(true);

        return request(next || url, method).then(response => {
            if (response && response['hydra:member']) {
                setData(data => data.concat(response['hydra:member']));
            }

            if (response && response['hydra:totalItems']) {
                setTotal(response['hydra:totalItems']);
            }

            if (response && response['hydra:view'] && response['hydra:view']['hydra:next']) {
                setNext(response['hydra:view']['hydra:next']);
            } else {
                setNext(null);
            }
        }).catch(
            error => error.json().then(data => console.error(data))
        ).finally(() => setLoading(false));
    }, [url, next]);

    return { data, total, loading, load, next: next !== null, setData };
}

export function useForm(url, method = 'POST', success) {
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState({});

    const load = useCallback((data) => {
        setLoading(true);

        return request(url, method, data)
            .then(data => data)
            .catch(error => {
                error.json().then(data => {
                    if (data.violations) {
                        setErrors(data.violations.reduce((o, violation) => {
                            o[violation.propertyPath] = violation.message;
    
                            return o;
                        }, {}));
                    }
                });

                throw error;
            }).finally(() => setLoading(false));
    }, [url, method, success]);

    return { loading, load, errors };
}
