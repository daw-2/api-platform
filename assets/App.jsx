import React, { useEffect } from 'react';
import { useFetch } from './hooks';

const Game = React.memo(({game}) => {
    let date = new Date(game.createdAt);

    return (
        <div className="border rounded-lg">
            <img src={game.contentUrl} alt={game.title} className="w-full h-64 object-cover rounded-t-lg" />
            <h2 className="text-center my-4 text-lg">
                {game.title}
                {game.user && <span> par {game.user.email}</span>}
            </h2>
            <p className="text-center mb-6">Le {date.toLocaleString(undefined, { dateStyle: 'medium', timeStyle: 'short' })}</p>
        </div>
    )
});

export default function App() {
    let { data: games, total, loading, load, next } = useFetch('/api/games');

    useEffect(() => load(), []);

    return (
        <div>
            <h2 className="text-center font-bold text-3xl py-8">{total} jeux au total</h2>
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
