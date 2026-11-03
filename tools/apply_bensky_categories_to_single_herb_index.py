#!/usr/bin/env python3
"""Attach the primary Bensky materia-medica category to every roster herb."""

from __future__ import annotations

import json
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
INDEX_PATH = ROOT / "blocks/myprogress/single-herb-card-index.json"

# The detailed-study route must follow the same primary Bensky category used by
# the roster. These entries previously inherited Board/module routing or no
# route at all, which produced a misleading "no primary category" annex.
GUIDE_NUMBER_OVERRIDES = {
    "Ban Lan Gen": 6,
    "Da Qing Ye": 6,
    "He Ye": 8,
}


CATEGORIES = {
    "warm-acrid-release-exterior": (
        "Warm, Acrid Herbs that Release the Exterior",
        "Hierbas tibias y acres que liberan el Exterior",
        "Bai Zhi, Cang Er Zi, Cong Bai, Fang Feng, Gao Ben, Gui Zhi, Jing Jie, "
        "Ma Huang, Qiang Huo, Sheng Jiang, Xi Xin, Xiang Ru, Xin Yi, Zi Su Ye",
    ),
    "cool-acrid-release-exterior": (
        "Cool, Acrid Herbs that Release the Exterior",
        "Hierbas frescas y acres que liberan el Exterior",
        "Bo He, Chai Hu, Chan Tui, Dan Dou Chi, Ge Gen, Ju Hua, Man Jing Zi, "
        "Mu Zei, Niu Bang Zi, Sang Ye, Sheng Ma",
    ),
    "clear-heat-drain-fire": (
        "Herbs that Clear Heat and Drain Fire",
        "Hierbas que aclaran Calor y drenan Fuego",
        "Dan Zhu Ye, Jue Ming Zi, Lian Zi Xin, Lu Gen, Shi Gao, Tian Hua Fen, "
        "Xia Ku Cao, Zhi Mu, Zhi Zi",
    ),
    "cool-blood": (
        "Herbs that Cool the Blood",
        "Hierbas que enfrían la Sangre",
        "Chi Shao, Mu Dan Pi, Qing Dai, Sheng Di Huang, Shui Niu Jiao, Xuan Shen, Zi Cao",
    ),
    "clear-heat-dry-dampness": (
        "Herbs that Clear Heat and Dry Dampness",
        "Hierbas que aclaran Calor y secan Humedad",
        "Bai Xian Pi, Huang Bai, Huang Lian, Huang Qin, Ku Shen, Long Dan Cao, Qin Pi",
    ),
    "clear-heat-resolve-toxicity": (
        "Herbs that Clear Heat and Resolve Toxicity",
        "Hierbas que aclaran Calor y resuelven toxicidad",
        "Bai Hua She She Cao, Bai Jiang Cao, Bai Tou Weng, Ban Lan Gen, Ban Zhi Lian, "
        "Chuan Xin Lian, Da Qing Ye, Dong Gua Zi, Jin Yin Hua, Lian Qiao, Ma Bo, "
        "Pu Gong Ying, Shan Dou Gen, She Gan, Tu Fu Ling, Ye Ju Hua, Yu Xing Cao",
    ),
    "clear-summerheat": (
        "Herbs that Clear Summerheat",
        "Hierbas que aclaran Calor de Verano",
        "He Ye, Lu Dou",
    ),
    "clear-deficiency-heat": (
        "Herbs that Clear Heat from Deficiency",
        "Hierbas que aclaran Calor por deficiencia",
        "Bai Wei, Di Gu Pi, Qing Hao",
    ),
    "downward-draining-purgatives": (
        "Downward-Draining Purgatives",
        "Purgantes que drenan hacia abajo",
        "Da Huang, Fan Xie Ye, Lu Hui, Mang Xiao",
    ),
    "moist-laxatives": (
        "Moist Laxatives",
        "Laxantes humectantes",
        "Huo Ma Ren",
    ),
    "harsh-expellants": (
        "Harsh Expellants",
        "Expulsantes drásticos",
        "Ba Dou, Gan Sui",
    ),
    "drain-dampness": (
        "Herbs that Drain Dampness",
        "Hierbas que drenan Humedad",
        "Bi Xie, Bian Xu, Che Qian Zi, Chi Xiao Dou, Chuan Mu Tong, Deng Xin Cao, "
        "Di Fu Zi, Dong Kui Zi, Fu Ling, Hai Jin Sha, Han Fang Ji, Hua Shi, "
        "Jin Qian Cao, Mu Tong, Qu Mai, Shi Wei, Tong Cao, Yi Yi Ren, Yin Chen, "
        "Ze Xie, Zhu Ling",
    ),
    "dispel-wind-dampness": (
        "Herbs that Dispel Wind-Dampness",
        "Hierbas que expulsan Viento-Humedad",
        "Bai Hua She, Cao Wu, Chuan Wu, Du Huo, Gou Ji, Hai Tong Pi, Lu Lu Tong, "
        "Mu Gua, Qin Jiao, Sang Ji Sheng, Sang Zhi, Si Gua Luo, Wei Ling Xian, "
        "Wu Jia Pi, Xi Xian Cao",
    ),
    "cool-transform-phlegm-heat": (
        "Herbs that Cool and Transform Phlegm-Heat",
        "Hierbas que enfrían y transforman Flema-Calor",
        "Chuan Bei Mu, Gua Lou, Gua Lou Pi, Gua Lou Ren, Hai Zao, Kun Bu, Qian Hu, "
        "Zhe Bei Mu, Zhu Ru",
    ),
    "warm-transform-cold-phlegm": (
        "Herbs that Warm and Transform Cold-Phlegm",
        "Hierbas que calientan y transforman Flema-Frío",
        "Bai Jie Zi, Bai Qian, Ban Xia, Jie Geng, Tian Nan Xing, Xuan Fu Hua, Zao Jiao Ci",
    ),
    "stop-coughing-calm-wheezing": (
        "Herbs that Stop Coughing and Calm Wheezing",
        "Hierbas que detienen la tos y calman las sibilancias",
        "Bai Bu, Bai Guo, Kuan Dong Hua, Pang Da Hai, Pi Pa Ye, Sang Bai Pi, "
        "Ting Li Zi, Xing Ren, Zi Su Zi, Zi Wan",
    ),
    "aromatic-transform-dampness": (
        "Aromatic Herbs that Transform Dampness",
        "Hierbas aromáticas que transforman Humedad",
        "Bai Dou Kou, Cang Zhu, Cao Dou Kou, Cao Guo, Hou Po, Huo Xiang, Pei Lan, Sha Ren",
    ),
    "relieve-food-stagnation": (
        "Herbs that Relieve Food Stagnation",
        "Hierbas que alivian estancamiento de alimentos",
        "Gu Ya (Setariae, Ji Nei Jin, Lai Fu Zi, Mai Ya, Shan Zha, Shen Qu",
    ),
    "regulate-qi": (
        "Herbs that Regulate the Qi",
        "Hierbas que regulan el Qi",
        "Chen Pi, Chen Xiang, Chuan Lian Zi, Da Fu Pi, Fo Shou, Ju Hong, Mei Gui Hua, "
        "Mu Xiang, Qing Pi, Tan Xiang, Wu Yao, Xiang Fu, Xie Bai, Zhi Ke, Zhi Shi",
    ),
    "invigorate-blood": (
        "Herbs that Invigorate the Blood",
        "Hierbas que vigorizan la Sangre",
        "Chuan Niu Xi, Chuan Xiong, Dan Shen, E Zhu, Hong Hua, Huai Niu Xi, "
        "Ji Xue Teng, Jiang Huang, Mo Yao, Ru Xiang, San Leng, Tao Ren, Tu Bie Chong, "
        "Wang Bu Liu Xing, Wu Ling Zhi, Yan Hu Suo, Yi Mu Cao, Yu Jin, Ze Lan",
    ),
    "stop-bleeding": (
        "Herbs that Stop Bleeding",
        "Hierbas que detienen el sangrado",
        "Ai Ye, Bai Ji, Bai Mao Gen, Ce Bai Ye, Da Ji, Di Yu, Huai Mi, Ou Jie, "
        "Pu Huang, Qian Cao, San Qi, Xian He Cao, Xiao Ji",
    ),
    "warm-interior-expel-cold": (
        "Herbs that Warm the Interior and Expel Cold",
        "Hierbas que calientan el Interior y expulsan Frío",
        "Bi Ba, Ding Xiang, Fu Zi, Gan Jiang, Gao Liang Jiang, "
        "Hu Jiao, Hua Jiao, Rou Gui, Wu Zhu Yu, Xiao Hui Xiang",
    ),
    "tonify-qi": (
        "Herbs that Tonify Qi",
        "Hierbas que tonifican Qi",
        "Bai Bian Dou, Bai Zhu, Ci Wu Jia, Da Zao, Dang Shen, Feng Mi, Gan Cao, "
        "Huang Jing, Huang Qi, Jing Mi, Ren Shen, Shan Yao, Tai Zi Shen, Xi Yang Shen, Yi Tang",
    ),
    "tonify-blood": (
        "Herbs that Tonify Blood",
        "Hierbas que tonifican Sangre",
        "Bai Shao, Dang Gui, E Jiao, He Shou Wu, Long Yan Rou, Shu Di Huang",
    ),
    "tonify-yang": (
        "Herbs that Tonify Yang",
        "Hierbas que tonifican Yang",
        "Ba Ji Tian, Bu Gu Zhi, Dong Chong Xia Cao, Du Zhong, Ge Jie, Gu Sui Bu, "
        "He Tao Ren, Lu Rong, Rou Cong Rong, Sha Yuan Ji Li, Suo Yang, Tu Si Zi, Xian Mao, "
        "Xu Duan, Yi Zhi Ren, Yin Yang Huo",
    ),
    "tonify-yin": (
        "Herbs that Tonify Yin",
        "Hierbas que tonifican Yin",
        "Bai He, Bie Jia, Gou Qi Zi, Gui Ban, Han Lian Cao, Hei Zhi Ma, Mai Men Dong, "
        "Nu Zhen Zi, Sang Shen, Sha Shen, Shi Hu, Tian Men Dong, Yu Zhu",
    ),
    "stabilize-bind": (
        "Herbs that Stabilize and Bind",
        "Hierbas que estabilizan y contienen",
        "Chi Shi Zhi, Chun Pi, Fu Pen Zi, Fu Xiao Mai, Hai Piao Xiao, He Zi, "
        "Jin Ying Zi, Lian Xu, Lian Zi, Ma Huang Gen, Qian Shi, Rou Dou Kou, "
        "Sang Piao Xiao, Shan Zhu Yu, Wu Mei, Wu Wei Zi",
    ),
    "anchor-settle-calm-spirit": (
        "Substances that Anchor, Settle, and Calm the Spirit",
        "Sustancias que anclan, asientan y calman el Shen",
        "Ci Shi, Long Gu, Mu Li, Zhen Zhu",
    ),
    "nourish-heart-calm-spirit": (
        "Herbs that Nourish the Heart and Calm the Spirit",
        "Hierbas que nutren el Corazón y calman el Shen",
        "Bai Zi Ren, Fu Shen, He Huan Pi, Ling Zhi, Suan Zao Ren, "
        "Ye Jiao Teng, Yuan Zhi",
    ),
    "open-orifices": (
        "Aromatic Substances that Open the Orifices",
        "Sustancias aromáticas que abren los orificios",
        "Bing Pian, Niu Huang, She Xiang, Shi Chang Pu",
    ),
    "extinguish-wind-stop-tremors": (
        "Substances that Extinguish Wind and Stop Tremors",
        "Sustancias que extinguen Viento y detienen temblores",
        "Bai Jiang Can, Ci Ji Li, Dai Zhe Shi, Di Long, Gou Teng, Ling Yang Jiao, "
        "Quan Xie, Shi Jue Ming, Tian Ma, Wu Gong",
    ),
    "expel-parasites": (
        "Herbs that Expel Parasites",
        "Hierbas que expulsan parásitos",
        "Bing Lang",
    ),
    "external-application": (
        "Substances for External Application",
        "Sustancias para aplicación externa",
        "Liu Huang, She Chuang Zi",
    ),
}


def split_names(value: str) -> list[str]:
    return [name.strip() for name in value.split(",") if name.strip()]


def main() -> None:
    catalog = json.loads(INDEX_PATH.read_text(encoding="utf-8"))
    herbs = catalog.get("herbs", [])
    index_names = [herb["name"] for herb in herbs]
    assignments: dict[str, dict[str, str]] = {}

    for code, (english, spanish, raw_names) in CATEGORIES.items():
        for name in split_names(raw_names):
            if name in assignments:
                raise SystemExit(f"Duplicate Bensky assignment: {name}")
            assignments[name] = {"code": code, "en": english, "es": spanish}

    missing = sorted(set(index_names) - set(assignments))
    extra = sorted(set(assignments) - set(index_names))
    if missing or extra:
        raise SystemExit(f"Bensky map mismatch; missing={missing!r}; extra={extra!r}")

    for herb in herbs:
        herb["benskyCategory"] = assignments[herb["name"]]
        if herb["name"] in GUIDE_NUMBER_OVERRIDES:
            herb["guideNumber"] = GUIDE_NUMBER_OVERRIDES[herb["name"]]

    catalog["routedToDetailedStudy"] = sum(
        herb.get("guideNumber") is not None or bool(herb.get("specialFocus"))
        for herb in herbs
    )

    catalog["benskySource"] = (
        "Bensky, Clavey, Stöger, and Gamble, Chinese Herbal Medicine: "
        "Materia Medica, 3rd ed. (primary monograph category)"
    )
    INDEX_PATH.write_text(
        json.dumps(catalog, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )
    print(f"Assigned primary Bensky categories to {len(herbs)} herbs.")


if __name__ == "__main__":
    main()
