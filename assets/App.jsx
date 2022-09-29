import React, { useCallback, useEffect, useRef } from 'react';
import { useFetch, useForm } from './hooks';

const Game = React.memo(({ game }) => {
    let date = new Date(game.createdAt);

    return (
        <div className="border rounded-lg">
            {game.image && <img src={game.contentUrl} alt={game.title} className="w-full h-64 object-cover rounded-t-lg" />}
            <h2 className="text-center my-4 text-lg">
                {game.title}
                {game.user && <span> par {game.user.email}</span>}
            </h2>
            <p className="text-center mb-6">Le {date.toLocaleString(undefined, { dateStyle: 'medium', timeStyle: 'short' })}</p>
        </div>
    )
});

const GameForm = React.memo(({ onGame }) => {
    let { load, loading, errors } = useForm('/api/games', 'POST');
    let title = useRef(null);
    let content = useRef(null);
    let onSubmit = useCallback((event) => {
        event.preventDefault();

        load({ title: title.current.value, content: content.current.value }).then(game => {
            onGame(game);
            title.current.value = content.current.value = '';
        });
    }, [load, title, content]);

    return (
        <form className="mb-8" onSubmit={onSubmit}>
            <div>
                <input ref={title} type="text" placeholder="Titre" />
                {errors.title && <p>{errors.title}</p>}
            </div>
            <div>
                <textarea ref={content} placeholder="Contenu"></textarea>
                {errors.content && <p>{errors.content}</p>}
            </div>

            <button disabled={loading} className="bg-blue-500 text-white rounded-lg px-4 py-3 duration-200 hover:opacity-50 disabled:opacity-50">
                Ajouter
            </button>
        </form>
    );
});

export default function App({ user }) {
    let { data: games, total, loading, load, next, setData: setGames } = useFetch('/api/games');

    useEffect(() => {
        load();
    }, []);

    return (
        <div>
            <h2 className="text-center font-bold text-3xl py-8">{total} jeux au total</h2>
            {user && <GameForm onGame={(game) => setGames(games => [game, ...games])} />}
            {loading && 'Chargement...'}

            <div className="grid grid-cols-3 gap-3">
                {games.map(game => <Game key={game.id} game={game} />)}
            </div>

            {next && (
                <div className="text-center mt-8">
                    <button disabled={loading} onClick={load} className="bg-blue-500 text-white rounded-lg px-4 py-3 duration-200 hover:opacity-50 disabled:opacity-50">
                        Charger les jeux
                    </button>
                </div>
            )}
        </div>
    );
}
