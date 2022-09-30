import React, { useCallback, useEffect, useRef, useState } from 'react';
import { useFetch, useForm } from './hooks';

const Game = React.memo(({ game, canEdit, onDelete, onUpdate }) => {
    let date = new Date(game.createdAt);
    let { load: loadDelete } = useFetch(game['@id'], 'DELETE');
    let [editing, setEditing] = useState(false);

    return (
        <div className="border rounded-lg">
            {game.image && <img src={game.contentUrl} alt={game.title} className="w-full h-64 object-cover rounded-t-lg" />}
            {editing ? <GameForm game={game} onGame={onUpdate} onCancel={() => setEditing(e => !e)} /> : <div>
                <h2 className="text-center my-4 text-lg">
                    {game.title}
                    {game.user && <span> par {game.user.email}</span>}
                </h2>
                <p className="text-center mb-6">Le {date.toLocaleString(undefined, { dateStyle: 'medium', timeStyle: 'short' })}</p>
            </div>}
            {canEdit && !editing && <div className="text-center">
                <button onClick={() => setEditing(e => !e)} className="bg-blue-500 text-white rounded-lg px-4 py-3 duration-200 hover:opacity-50 disabled:opacity-50">
                    Modifier
                </button>
                <button onClick={() => loadDelete().then(() => onDelete(game))} className="bg-red-500 text-white rounded-lg px-4 py-3 duration-200 hover:opacity-50 disabled:opacity-50">
                    Supprimer
                </button>
            </div>}
        </div>
    )
});

const GameForm = React.memo(({ onGame, game = null, onCancel = null }) => {
    let url = game ? game['@id'] : '/api/games';
    let method = game ? 'PUT' : 'POST';
    let { load, loading, errors } = useForm(url, method);
    let title = useRef(null);
    let content = useRef(null);
    let onSubmit = useCallback((event) => {
        event.preventDefault();

        load({ title: title.current.value, content: content.current.value }).then(game => {
            onGame(game);
            title.current.value = content.current.value = '';
            onCancel && onCancel();
        });
    }, [load, title, content]);

    useEffect(() => {
        if (game?.title && title.current) title.current.value = game.title;
        if (game?.content && content.current) content.current.value = game.content;
    }, [game]);

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
                {game ? 'Editer' : 'Ajouter'}
            </button>

            {onCancel && <button type="button" onClick={onCancel} className="bg-red-500 text-white rounded-lg px-4 py-3 duration-200 hover:opacity-50 disabled:opacity-50">
                Annuler
            </button>}
        </form>
    );
});

export default function App({ user }) {
    let { data: games, total, loading, load, next, setData: setGames } = useFetch('/api/games');

    useEffect(() => {
        load();

        const hub = new URL(document.getElementById('mercure-url').textContent);
        hub.searchParams.append('topic', '/topic/{id}');
        const sse = new EventSource(hub, { withCredentials: true });
        sse.onmessage = event => {
            let data = JSON.parse(event.data);

            setGames(games => games.map(g => {
                g.title = data.message;

                return g;
            }))
        };

        return () => sse.close();
    }, []);

    return (
        <div>
            <h2 className="text-center font-bold text-3xl py-8">{total} jeux au total</h2>
            {user && <GameForm onGame={(game) => setGames(games => [game, ...games])} />}
            {loading && 'Chargement...'}

            <div className="grid grid-cols-3 gap-3">
                {games.map(game => <Game
                    key={game.id}
                    game={game}
                    canEdit={game.user && user == game.user.id}
                    onDelete={game => setGames(games => games.filter(g => g.id !== game.id))}
                    onUpdate={game => setGames(games => games.map(g => g.id === game.id ? game : g))} />
                )}
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
