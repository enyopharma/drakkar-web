import React, { useState } from "react"
import Swal from "sweetalert2"
import withReactContent from "sweetalert2-react-content"

import { Run, Publication, Peptide, PeptideData, Hotspots } from "../src/types"

const MySwal = withReactContent(Swal)

type PeptideFormProps = {
    run: Run
    publication: Publication
    peptide: Peptide
}

export const PeptideForm: React.FC<PeptideFormProps> = ({ run, publication, peptide }) => {
    const [current, setCurrent] = useState<PeptideData>(peptide.data)
    const [cter, setCter] = useState<string>(current.cter)
    const [nter, setNter] = useState<string>(current.nter)
    const [affType, setAffType] = useState<string>(current.affinity.type)
    const [affValue, setAffValue] = useState<number | null>(current.affinity.value)
    const [affUnit, setAffUnit] = useState<string>(current.affinity.unit)
    const [hotspots, setHotspots] = useState<Hotspots>(current.hotspots)
    const [xprMethod, setXprMethod] = useState<string>(current.methods.expression)
    const [itxMethod, setItxMethod] = useState<string>(current.methods.interaction)
    const [info, setInfo] = useState<string>(current.info)
    const [saving, setSaving] = useState<boolean>(false)

    const data = {
        type: peptide.type,
        sequence: peptide.sequence,
        cter: cter.trim(),
        nter: nter.trim(),
        affinity: {
            type: affType.trim(),
            value: affValue,
            unit: affUnit.trim(),
        },
        hotspots,
        methods: {
            expression: xprMethod.trim(),
            interaction: itxMethod.trim(),
        },
        info: info.trim(),
    }

    const disabled = saving || (
        current.cter === data.cter &&
        current.nter === data.nter &&
        current.affinity.type === data.affinity.type &&
        current.affinity.value === data.affinity.value &&
        current.affinity.unit === data.affinity.unit &&
        sameHotspots(current.hotspots, data.hotspots) &&
        current.methods.expression === data.methods.expression &&
        current.methods.interaction === data.methods.interaction &&
        current.info === data.info
    )

    const save = async () => {
        setSaving(true)

        const result = await savePeptide(run, publication, peptide, data)

        if (result.success) {
            setCurrent(data)
            MySwal.fire({
                icon: 'success',
                text: 'Peptide data successfully saved!',
            })
        } else {
            MySwal.fire({
                icon: 'error',
                title: <p>Something went wrong</p>,
                html: (
                    <ul className="list-unstyled">
                        {result.errors.map((e, i) => <li key={i}>{e}</li>)}
                    </ul>
                ),
            })
        }

        setSaving(false)
    }

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
                        value={cter}
                        onChange={e => setCter(e.target.value)}
                        disabled={saving}
                    />
                </div>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-nter`}>Nter</label>
                    <input
                        id={`${peptide.type}-nter`}
                        type="text"
                        className="form-control"
                        value={nter}
                        onChange={e => setNter(e.target.value)}
                        disabled={saving}
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
                        value={affType}
                        onChange={e => setAffType(e.target.value)}
                        placeholder="IC50, IC95, EC50, Ki, Kd, Km, Potency, Inhibition ..."
                        disabled={saving}
                    />
                </div>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-aff-value`}>Value</label>
                    <input
                        id={`${peptide.type}-aff-value`}
                        type="number"
                        className="form-control"
                        value={affValue === null ? '' : affValue}
                        onChange={e => setAffValue(e.target.value === '' ? null : parseFloat(e.target.value))}
                        disabled={saving}
                        step="any"
                    />
                </div>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-aff-unit`}>Unit</label>
                    <input
                        id={`${peptide.type}-aff-unit`}
                        type="text"
                        className="form-control"
                        onChange={e => setAffUnit(e.target.value)}
                        value={affUnit}
                        placeholder="µM, nM, % ..."
                        disabled={saving}
                    />
                </div>
            </fieldset>
            <HotspotFieldset peptide={peptide} hotspots={hotspots} update={setHotspots} disabled={saving} />
            <fieldset>
                <legend>Additional info</legend>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-xpr`}>Expression method</label>
                    <input
                        id={`${peptide.type}-xpr`}
                        type="text"
                        className="form-control"
                        value={xprMethod}
                        onChange={e => setXprMethod(e.target.value)}
                        disabled={saving}
                    />
                </div>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-itx`}>Interaction method</label>
                    <input
                        id={`${peptide.type}-itx`}
                        type="text"
                        className="form-control"
                        value={itxMethod}
                        onChange={e => setItxMethod(e.target.value)}
                        disabled={saving}
                    />
                </div>
                <div className="form-group">
                    <label htmlFor={`${peptide.type}-free`}>Free text</label>
                    <textarea
                        id={`${peptide.type}-free`}
                        className="form-control"
                        value={info}
                        onChange={e => setInfo(e.target.value)}
                        placeholder="Disruption of interaction, inhibition of enzymatic activity, ..."
                        disabled={saving}
                        rows={3}
                    ></textarea>
                </div>
            </fieldset>
            <button type="button" className="btn btn-block btn-primary" disabled={disabled} onClick={save}>
                Save peptide infos
            </button>
        </form>
    )
}

type HotspotFieldsetProps = {
    peptide: Peptide
    hotspots: Hotspots
    update: (hotspots: Hotspots) => void
    disabled: boolean
}

const HotspotFieldset: React.FC<HotspotFieldsetProps> = ({ peptide, hotspots, update, disabled }) => {
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
                        disabled={disabled}
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
                    disabled={disabled}
                />
                <div className="input-group-append">
                    <button
                        type="button"
                        className="btn btn-primary"
                        onClick={() => saveHotspot()}
                        disabled={disabled || index === null}
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
                                    disabled={disabled}
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
    if (Object.keys(hotspots1).length !== Object.keys(hotspots2).length) {
        return false
    }

    for (const istr of Object.keys(hotspots1)) {
        const i = parseInt(istr)

        if (hotspots2[i] === undefined) {
            return false
        }

        if (hotspots1[i].trim() !== hotspots2[i].trim()) {
            return false
        }
    }

    return true
}

type Result = { success: true } | { success: false, errors: string[] }

const savePeptide = async (run: Run, publication: Publication, peptide: Peptide, data: PeptideData): Promise<Result> => {
    const url = `/runs/${run.id}/publications/${publication.pmid}/descriptions/${peptide.description_id}/peptides`

    const params = {
        method: 'POST',
        headers: {
            'accept': 'application/json',
            'content-type': 'application/json',
        },
        body: JSON.stringify(data),
    }

    try {
        const response = await fetch(url, params)
        const json = await response.json()
        return json
    }

    catch (e) { console.log(e) }

    return { success: false, errors: [] }
}
