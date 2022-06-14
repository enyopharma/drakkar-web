import React, { useState } from "react"

import { Peptide, Hotspots } from "../src/types"

type PeptideFormProps = {
    peptide: Peptide
}

export const PeptideForm: React.FC<PeptideFormProps> = ({ peptide }) => {
    const [cter, setCter] = useState<string>(peptide.data.cter)
    const [nter, setNter] = useState<string>(peptide.data.nter)
    const [affType, setAffType] = useState<string>(peptide.data.affinity.type)
    const [affValue, setAffValue] = useState<number | null>(peptide.data.affinity.value)
    const [affUnit, setAffUnit] = useState<string>(peptide.data.affinity.unit)
    const [hotspots, setHotspots] = useState<Hotspots>(peptide.data.hotspots)
    const [xprMethod, setXprMethod] = useState<string>(peptide.data.methods.expression)
    const [itxMethod, setItxMethod] = useState<string>(peptide.data.methods.interaction)
    const [info, setInfo] = useState<string>(peptide.data.info)

    const data = {
        cter: cter.trim(),
        nter: nter.trim(),
        affType: affType.trim(),
        affValue: affValue,
        affUnit: affUnit.trim(),
        hotspots,
        xprMethod: xprMethod.trim(),
        itxMethod: itxMethod.trim(),
        info: info.trim(),
    }

    const same =
        peptide.data.cter === cter &&
        peptide.data.nter === nter &&
        peptide.data.affinity.type === affType &&
        peptide.data.affinity.value === affValue &&
        peptide.data.affinity.unit === affUnit &&
        sameHotspots(peptide.data.hotspots, hotspots) &&
        peptide.data.methods.expression === xprMethod &&
        peptide.data.methods.interaction === itxMethod &&
        peptide.data.info === info

    console.log(data)

    return (
        <form>
            <fieldset>
                <legend>Additional sequences</legend>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-cter`}>Cter</label>
                    <input
                        id={`${peptide.type}-cter`}
                        type="text"
                        className="form-control"
                        onChange={e => setCter(e.target.value)}
                    />
                </div>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-nter`}>Nter</label>
                    <input
                        id={`${peptide.type}-nter`}
                        type="text"
                        className="form-control"
                        onChange={e => setNter(e.target.value)}
                    />
                </div>
            </fieldset>
            <fieldset>
                <legend>Target affinity</legend>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-aff-type`}>Type</label>
                    <input
                        id={`${peptide.type}-aff-type`}
                        type="text"
                        className="form-control"
                        onChange={e => setAffType(e.target.value)}
                        placeholder="IC50, IC95, EC50, Ki, Kd, Km, Potency, Inhibition ..."
                    />
                </div>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-aff-value`}>Value</label>
                    <input
                        id={`${peptide.type}-aff-value`}
                        type="number"
                        className="form-control"
                        onChange={e => setAffValue(parseInt(e.target.value))}
                    />
                </div>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-aff-unit`}>Unit</label>
                    <input
                        id={`${peptide.type}-aff-unit`}
                        type="text"
                        className="form-control"
                        onChange={e => setAffUnit(e.target.value)}
                        placeholder="µM, nM, % ..."
                    />
                </div>
            </fieldset>
            <HotspotFieldset peptide={peptide} hotspots={hotspots} update={setHotspots} />
            <fieldset>
                <legend>Additional info</legend>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-xpr`}>Expression method</label>
                    <input
                        id={`${peptide.type}-xpr`}
                        type="text"
                        className="form-control"
                        onChange={e => setXprMethod(e.target.value)}
                    />
                </div>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-itx`}>Interaction method</label>
                    <input
                        id={`${peptide.type}-itx`}
                        type="text"
                        className="form-control"
                        onChange={e => setItxMethod(e.target.value)}
                    />
                </div>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-free`}>Free text</label>
                    <textarea
                        id={`${peptide.type}-free`}
                        className="form-control"
                        onChange={e => setInfo(e.target.value)}
                        placeholder="Disruption of interaction, inhibition of enzymatic activity, ..."
                        rows={3}
                    ></textarea>
                </div>
            </fieldset>
            <button type="button" className="btn btn-block btn-primary" disabled={same}>
                Save peptide infos
            </button>
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

        newHotspots[index] = description.trim()

        update(newHotspots)

        setIndex(null)
        setDescription('')
    }

    const removeHotspot = (i: number) => {
        const newHotspots = { ...hotspots }

        delete newHotspots[i]

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
                        Add
                    </button>
                </div>
            </div>
            {Object.values(hotspots).length > 0 && (
                <ul className="list-group list-group-flush">
                    {Object.keys(hotspots).map((istr, j) => {
                        const i = parseInt(istr)

                        return (
                            <li key={j} className="list-group-item">
                                <strong>{peptide.sequence[i]}[{i + 1}]</strong>
                                {hotspots[i] && ` - ${hotspots[i]}`}
                                <button
                                    type="button"
                                    className="btn btn-sm btn-danger float-right"
                                    onClick={() => removeHotspot(i)}
                                >Delete</button>
                            </li>
                        )
                    })}
                </ul>
            )}
        </fieldset>
    )
}

const sameHotspots = (hotspots1: Hotspots, hotspots2: Hotspots) => {
    return true
}
