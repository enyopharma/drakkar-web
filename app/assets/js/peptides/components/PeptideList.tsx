import React, { useState } from 'react'

import { PeptideType, Peptide, Hotspots } from '../src/types'

type PeptideListProps = {
    type: PeptideType
    peptides: Peptide[]
}

export const PeptideList: React.FC<PeptideListProps> = ({ type, peptides }) => {
    const filtered = peptides.filter(p => p.type === type)

    const [peptide, setPeptide] = useState<Peptide | null>(filtered.length === 0 ? null : peptides[0])

    const update = (sequence: string) => {
        const filtered = peptides.filter(p => p.sequence === sequence)

        if (filtered.length !== 1) {
            throw new Error('wtf')
        }

        setPeptide(filtered[0])
    }

    return (
        <div className="card">
            <h3 className="card-header">
                {type === 'h' ? 'Human peptides' : 'Viral peptides'}
            </h3>
            {peptide === null && (
                <div className="card-body">
                    {type === 'h' ? 'No human peptides' : 'No viral peptides'}
                </div>
            )}
            {peptide !== null && (
                <div className="card-body">
                    <p>
                        <select className="form-control" onChange={e => update(e.target.value)} value={peptide.sequence}>
                            {filtered.map((peptide, i) => (
                                <option key={i + 1} value={peptide.sequence}>
                                    {peptide.sequence}
                                </option>
                            ))}
                        </select>
                    </p>
                    <PeptideForm peptide={peptide} />
                </div>
            )}
        </div>
    )
}

type PeptideFormProps = {
    peptide: Peptide
}

const PeptideForm: React.FC<PeptideFormProps> = ({ peptide }) => {
    const [hotspots, setHotspots] = useState<Hotspots>(peptide.data.hotspots)

    return (
        <form>
            <HotspotFieldset peptide={peptide} hotspots={hotspots} update={setHotspots} />
        </form>
    )
}

type HotspotFieldsetProps = {
    peptide: Peptide
    hotspots: Hotspots
    update: (hotspots: Hotspots) => void
}

const HotspotFieldset: React.FC<HotspotFieldsetProps> = ({ peptide, hotspots, update }) => {
    const [index, setIndex] = useState<number | null>(null)
    const [description, setDescription] = useState<string>('')

    const saveHotspot = () => {
        if (index === null) return

        const newHotspots = { ...hotspots }

        newHotspots[index] = description

        update(newHotspots)

        setIndex(null)
        setDescription('')
    }

    const removeHotspot = (aa: number) => {
        const newHotspots = { ...hotspots }

        delete newHotspots[aa]

        update(newHotspots)
    }

    const aas = peptide.sequence.split('')

    return (
        <fieldset>
            <legend>Hotspots</legend>
            <div className="btn-group mb-3">
                {aas.map((aa, i) => (
                    <button
                        key={i}
                        type="button"
                        className={`btn ${i === index ? 'btn-primary' : 'btn-outline-primary'}`}
                        onClick={() => setIndex(i)}
                    >
                        {aa}
                    </button>
                ))}
            </div>
            <div className="input-group mb-3">
                <input
                    type="text"
                    className="form-control"
                    value={description}
                    onChange={e => setDescription(e.target.value)}
                    placeholder="Hotspot description"
                />
                <div className="input-group-append">
                    <button
                        type="button"
                        className="btn btn-primary"
                        onClick={() => saveHotspot()} disabled={index === null}
                    >
                        Save
                    </button>
                </div>
            </div>
            {Object.values(hotspots).length > 0 && (
                <ul>
                    {Object.keys(hotspots).map((aastr, i) => {
                        const aa = parseInt(aastr)

                        return (
                            <li key={i}>
                                [<button
                                    type="button"
                                    className="btn btn-link"
                                    onClick={() => removeHotspot(aa)}
                                    style={{ padding: 0 }}
                                >x</button>] &nbsp;
                                <strong>{peptide.sequence[aa]}[{aa + 1}]</strong>
                                {hotspots[aa] && `- ${hotspots[aa]}`}
                            </li>
                        )
                    })}
                </ul>
            )}
        </fieldset>
    )
}
